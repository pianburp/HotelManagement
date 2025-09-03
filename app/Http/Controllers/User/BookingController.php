<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\RoomAvailabilityCacheService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheService = app(RoomAvailabilityCacheService::class);
        
        // Try to get cached room stats first
        $cachedStats = $cacheService->getRoomStats();
        
        $rooms = Room::with(['roomType.translations'])
            ->whereHas('roomType')
            ->where('status', 'available') // Only show available rooms
            ->orderBy('room_type_id') // Order by room type to show variety
            ->orderBy('room_number') // Then by room number
            ->paginate(9);

        $roomTypes = $this->getCachedRoomTypes();
        
        $amenities = config('hotel.amenities', []);

        // Get price range from available room types (cached)
        $priceRange = $this->getCachedPriceRange();
        
        $minPrice = $priceRange->min_price ?? 0;
        $maxPrice = $priceRange->max_price ?? 1000;

        // Add status colors for the view
        $statusColors = [
            'available' => 'bg-green-500',
            'reserved' => 'bg-blue-500',
            'occupied' => 'bg-red-500',
            'maintenance' => 'bg-yellow-500'
        ];

        return view('user.rooms.index', compact('rooms', 'roomTypes', 'amenities', 'statusColors', 'minPrice', 'maxPrice'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $room = Room::with(['roomType.translations'])->findOrFail($request->room);
        
        // Set default values for pricing display
        $numberOfNights = 1; // Default to 1 night for display
        $subtotal = $room->roomType->base_price; // Default to 1 night rate
        $taxes = $subtotal * 0.10; // 10% tax rate
        $total = $subtotal + $taxes;
        
        // Calculate actual booking details if dates are provided
        if ($request->filled(['check_in', 'check_out'])) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            
            if ($checkOut > $checkIn) {
                $numberOfNights = $checkIn->diffInDays($checkOut);
                $subtotal = $room->roomType->base_price * $numberOfNights;
                $taxes = $subtotal * 0.10;
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
        $user = auth()->user();
        
        // Calculate total amount
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);
        $subtotal = $room->roomType->base_price * $nights;
        $taxes = $subtotal * 0.10; // 10% tax rate
        $totalAmount = $subtotal + $taxes;

        // Store booking data in session for payment processing
        session([
            'booking.room_id' => $request->room_id,
            'booking.room_type' => $room->roomType->name,
            'booking.room_number' => $room->room_number,
            'booking.check_in' => $request->check_in,
            'booking.check_out' => $request->check_out,
            'booking.guests' => $request->guests,
            'booking.special_requests' => $request->special_requests,
            'booking.payment_method' => $request->payment_method,
            'booking.nights' => $nights,
            'booking.subtotal' => $subtotal,
            'booking.taxes' => $taxes,
            'booking.total' => 'RM ' . number_format($totalAmount, 2),
            'booking.total_amount' => $totalAmount,
            'booking.guest_name' => $user->name,
            'booking.guest_email' => $user->email,
            'booking.guest_phone' => $user->phone,
        ]);

        // Redirect to payment demo instead of creating booking directly
        return redirect()->route('user.payments.demo');
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
     * Search for available rooms based on criteria with Redis caching
     */
    public function search(Request $request)
    {
        // Validate the search inputs
        $request->validate([
            'check_in' => 'nullable|date|after_or_equal:today',
            'check_out' => 'nullable|date|after:check_in',
            'occupancy' => 'nullable|integer|min:1|max:6',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0|gte:price_min',
            'room_type' => 'nullable|exists:room_types,id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|in:' . implode(',', config('hotel.amenities')),
        ]);

        $cacheService = app(RoomAvailabilityCacheService::class);
        
        // Create search parameters for caching
        $searchParams = $request->only([
            'check_in', 'check_out', 'occupancy', 'price_min', 
            'price_max', 'room_type', 'amenities'
        ]);
        
        // Try to get cached search results
        $cachedResults = $cacheService->getSearchResults($searchParams);
        if ($cachedResults !== null) {
            $rooms = Room::with(['roomType.translations', 'roomType.media'])
                ->whereIn('id', $cachedResults)
                ->paginate(9)
                ->withQueryString();
        } else {
            // Perform the search
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

            // Filter by amenities (room must have ALL selected amenities)
            if ($request->filled('amenities')) {
                $selectedAmenities = $request->amenities;
                $query->whereHas('roomType', function ($q) use ($selectedAmenities) {
                    foreach ($selectedAmenities as $amenity) {
                        $q->whereJsonContains('amenities', $amenity);
                    }
                });
            }

            // Filter by date range and availability
            if ($request->filled(['check_in', 'check_out'])) {
                $checkIn = $request->check_in;
                $checkOut = $request->check_out;
                
                // Use cached availability if possible
                $availableRoomIds = $cacheService->getAvailableRoomsForDateRange(
                    $checkIn, 
                    $checkOut, 
                    $searchParams
                );
                
                if ($availableRoomIds !== null) {
                    $query->whereIn('id', $availableRoomIds);
                } else {
                    // Only show rooms that are actually available and don't have conflicting bookings
                    $query->where('status', 'available')
                        ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                            $q->where(function ($q) use ($checkIn, $checkOut) {
                                // Check for any overlap in booking dates
                                $q->where(function ($overlap) use ($checkIn, $checkOut) {
                                    // Case 1: Existing booking starts during our stay
                                    $overlap->whereBetween('check_in_date', [$checkIn, $checkOut])
                                        // Case 2: Existing booking ends during our stay  
                                        ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                                        // Case 3: Our stay is completely within existing booking
                                        ->orWhere(function ($within) use ($checkIn, $checkOut) {
                                            $within->where('check_in_date', '<=', $checkIn)
                                                   ->where('check_out_date', '>=', $checkOut);
                                        })
                                        // Case 4: Existing booking is completely within our stay
                                        ->orWhere(function ($contains) use ($checkIn, $checkOut) {
                                            $contains->where('check_in_date', '>=', $checkIn)
                                                    ->where('check_out_date', '<=', $checkOut);
                                        });
                                });
                            })->whereIn('status', ['confirmed', 'checked_in', 'pending']);
                        });
                        
                    // Cache the available room IDs for this date range
                    $availableRoomIds = $query->pluck('id')->toArray();
                    $cacheService->cacheAvailableRoomsForDateRange($checkIn, $checkOut, $searchParams, $availableRoomIds);
                }
            } else {
                // If no dates specified, only show available rooms
                $query->where('status', 'available');
            }

            $rooms = $query->paginate(9)->withQueryString();
            
            // Cache the search results
            $roomIds = $rooms->pluck('id')->toArray();
            $cacheService->cacheSearchResults($searchParams, $roomIds);
        }

        $roomTypes = $this->getCachedRoomTypes();
        
        $amenities = config('hotel.amenities', []);

        // Get price range from available room types (cached)
        $priceRange = $this->getCachedPriceRange();
        
        $minPrice = $priceRange->min_price ?? 0;
        $maxPrice = $priceRange->max_price ?? 1000;

        return view('user.rooms.index', compact('rooms', 'roomTypes', 'amenities', 'minPrice', 'maxPrice'));
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

    /**
     * Get cached room types with fallback for non-tagging cache stores
     */
    private function getCachedRoomTypes()
    {
        $supportsTagging = $this->supportsTagging();
        
        if ($supportsTagging) {
            return Cache::tags(['room_types'])->remember('room_types_active', 3600, function () {
                return RoomType::with('translations')->where('is_active', true)->get();
            });
        } else {
            return Cache::remember('room_types_active', 3600, function () {
                return RoomType::with('translations')->where('is_active', true)->get();
            });
        }
    }

    /**
     * Get cached price range with fallback for non-tagging cache stores
     */
    private function getCachedPriceRange()
    {
        $supportsTagging = $this->supportsTagging();
        
        if ($supportsTagging) {
            return Cache::tags(['room_types'])->remember('price_range', 3600, function () {
                return RoomType::where('is_active', true)
                    ->selectRaw('MIN(base_price) as min_price, MAX(base_price) as max_price')
                    ->first();
            });
        } else {
            return Cache::remember('price_range', 3600, function () {
                return RoomType::where('is_active', true)
                    ->selectRaw('MIN(base_price) as min_price, MAX(base_price) as max_price')
                    ->first();
            });
        }
    }

    /**
     * Check if the current cache driver supports tagging
     */
    private function supportsTagging(): bool
    {
        $driver = config('cache.default');
        $supportedDrivers = ['redis', 'memcached'];
        return in_array($driver, $supportedDrivers);
    }
}
