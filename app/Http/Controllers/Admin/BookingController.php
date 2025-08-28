<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['room.roomType.translations', 'user', 'payment']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('check_in_from')) {
            $query->where('check_in', '>=', $request->check_in_from);
        }

        if ($request->filled('check_in_to')) {
            $query->where('check_in', '<=', $request->check_in_to);
        }

        if ($request->filled('guest_name')) {
            $query->where('guest_name', 'like', '%' . $request->guest_name . '%');
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'checked_out' => Booking::where('status', 'checked_out')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roomTypes = RoomType::with('translations')->where('is_active', true)->get();
        $rooms = Room::with('roomType.translations')->where('status', 'available')->get();
        
        return view('admin.bookings.create', compact('roomTypes', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $room = Room::findOrFail($request->room_id);
            $nights = Carbon::parse($request->check_in)->diffInDays(Carbon::parse($request->check_out));
            $totalAmount = $room->roomType->base_price * $nights;

            $booking = Booking::create([
                'booking_number' => 'BK' . time() . rand(100, 999),
                'room_id' => $request->room_id,
                'user_id' => auth()->id(),
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'number_of_guests' => $request->number_of_guests,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'special_requests' => $request->special_requests,
            ]);

            // Update room status
            $room->update(['status' => 'reserved']);

            DB::commit();

            return redirect()->route('admin.bookings.index')
                           ->with('success', 'Booking created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $booking->load(['room.roomType.translations', 'user', 'payment', 'statusHistory']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->route('admin.bookings.show', $booking)
                           ->with('error', 'This booking cannot be edited.');
        }

        $roomTypes = RoomType::with('translations')->where('is_active', true)->get();
        $rooms = Room::with('roomType.translations')->where('status', 'available')->get();
        
        return view('admin.bookings.edit', compact('booking', 'roomTypes', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->route('admin.bookings.show', $booking)
                           ->with('error', 'This booking cannot be updated.');
        }

        $this->validate($request, [
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldRoom = $booking->room;
            $newRoom = Room::findOrFail($request->room_id);
            
            $nights = Carbon::parse($request->check_in)->diffInDays(Carbon::parse($request->check_out));
            $totalAmount = $newRoom->roomType->base_price * $nights;

            $booking->update([
                'room_id' => $request->room_id,
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'number_of_guests' => $request->number_of_guests,
                'total_amount' => $totalAmount,
                'special_requests' => $request->special_requests,
            ]);

            // Update room statuses if room changed
            if ($oldRoom->id !== $newRoom->id) {
                $oldRoom->update(['status' => 'available']);
                $newRoom->update(['status' => 'reserved']);
            }

            DB::commit();

            return redirect()->route('admin.bookings.index')
                           ->with('success', 'Booking updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating booking: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        if ($booking->status === 'checked_out') {
            return back()->with('error', 'Cannot delete a checked-out booking.');
        }

        DB::beginTransaction();
        try {
            // Free up the room if booking is not cancelled
            if ($booking->status !== 'cancelled') {
                $booking->room->update(['status' => 'available']);
            }

            $booking->delete();
            DB::commit();

            return redirect()->route('admin.bookings.index')
                           ->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error deleting booking: ' . $e->getMessage());
        }
    }

    /**
     * Confirm a pending booking.
     */
    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $booking->update(['status' => 'confirmed']);
        $booking->room->update(['status' => 'reserved']);

        return back()->with('success', 'Booking confirmed successfully.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->status === 'checked_out') {
            return back()->with('error', 'Cannot cancel a checked-out booking.');
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
            ]);

            // Free up the room
            $booking->room->update(['status' => 'available']);

            DB::commit();

            return back()->with('success', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error cancelling booking: ' . $e->getMessage());
        }
    }
}
