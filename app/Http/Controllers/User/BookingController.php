<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with(['roomType.translations'])
            ->whereHas('roomType')
            ->orderBy('room_type_id') // Order by room type to show variety
            ->orderBy('room_number') // Then by room number
            ->paginate(9);

        $roomTypes = RoomType::with('translations')->get();
        $amenities = config('hotel.amenities', []);

        // Add status colors for the view
        $statusColors = [
            'available' => 'bg-green-500',
            'reserved' => 'bg-blue-500',
            'occupied' => 'bg-red-500',
            'maintenance' => 'bg-yellow-500'
        ];

        return view('user.rooms.index', compact('rooms', 'roomTypes', 'amenities', 'statusColors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $room = Room::with(['roomType.translations'])->findOrFail($request->room);
        
        // Calculate booking details if dates are provided
        $numberOfNights = 0;
        $subtotal = 0;
        $taxes = 0;
        $total = 0;
        
        if ($request->filled(['check_in', 'check_out'])) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            
            if ($checkOut > $checkIn) {
                $numberOfNights = $checkIn->diffInDays($checkOut);
                $subtotal = $room->roomType->base_price * $numberOfNights;
                $taxes = $subtotal * 0.10; // 10% tax rate
                $total = $subtotal + $taxes;
            }
        }
        
        return view('user.bookings.create', compact(
            'room', 
            'numberOfNights', 
            'subtotal', 
            'taxes', 
            'total'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingRequest $request)
    {
        $room = Room::with('roomType')->findOrFail($request->room_id);
        
        // Calculate total amount
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);
        $subtotal = $room->roomType->base_price * $nights;
        $taxes = $subtotal * 0.10; // 10% tax rate
        $totalAmount = $subtotal + $taxes;

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $request->room_id,
            'check_in_date' => $request->check_in,
            'check_out_date' => $request->check_out,
            'guests_count' => $request->guests,
            'special_requests' => $request->special_requests,
            'total_amount' => $totalAmount,
            'status' => 'confirmed',
            'booking_reference' => $this->generateBookingReference(),
            'booking_source' => 'website',
        ]);

        // Update room status to reserved
        $room->update(['status' => 'reserved']);

        return redirect()->route('user.bookings.show', $booking)
            ->with('success', 'Booking created successfully!');
    }

    /**
     * Display the user's booking history.
     */
    public function history(Request $request)
    {
        $query = Booking::with(['room.roomType.translations', 'payments'])
            ->where('user_id', auth()->id());

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('check_out', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('user.bookings.history', compact('bookings'));
    }

    /**
     * Display the specified booking.
     */
    public function showBooking(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['room.roomType.translations', 'payments']);
        
        return view('user.bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Booking $booking)
    {
        // Ensure user can only cancel their own bookings
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow cancellation of confirmed bookings that haven't started yet
        if ($booking->status !== 'confirmed' || $booking->check_in->isPast()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('user.bookings.history')
            ->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Show room details.
     */
    public function show(Room $room)
    {
        $room->load(['roomType.translations', 'roomType.media']);
        
        return view('user.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Search for available rooms based on criteria
     */
    public function search(Request $request)
    {
        $query = Room::with(['roomType.translations', 'roomType.media'])
            ->whereHas('roomType');

        // Filter by room type
        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        // Filter by occupancy
        if ($request->filled('occupancy')) {
            $query->whereHas('roomType', function ($q) use ($request) {
                $q->where('max_occupancy', '>=', $request->occupancy);
            });
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->whereHas('roomType', function ($q) use ($request) {
                $q->where('base_price', '>=', $request->price_min);
            });
        }
        if ($request->filled('price_max')) {
            $query->whereHas('roomType', function ($q) use ($request) {
                $q->where('base_price', '<=', $request->price_max);
            });
        }

        // Filter by amenities
        if ($request->filled('amenities')) {
            $query->whereHas('roomType', function ($q) use ($request) {
                foreach ($request->amenities as $amenity) {
                    $q->whereJsonContains('amenities', $amenity);
                }
            });
        }

        // Filter by date range and availability
        if ($request->filled(['check_in', 'check_out'])) {
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;
            
            $query->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->where(function ($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
                })->whereIn('status', ['confirmed', 'checked_in']);
            });
        }

        $rooms = $query->paginate(9)->withQueryString();
        $roomTypes = RoomType::with('translations')->get();
        $amenities = config('hotel.amenities', []);

        return view('user.rooms.index', compact('rooms', 'roomTypes', 'amenities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }

    /**
     * Generate a unique booking reference.
     */
    private function generateBookingReference(): string
    {
        do {
            $reference = 'BK' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Booking::where('booking_reference', $reference)->exists());

        return $reference;
    }
}
