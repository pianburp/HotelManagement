<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with(['roomType'])
            ->whereHas('roomType')
            ->paginate(9);

        $roomTypes = RoomType::all();
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
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
        $query = Room::with(['roomType.media'])
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
                    $q->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                      });
                })->whereIn('status', ['confirmed', 'checked_in']);
            });
        }

        $rooms = $query->paginate(9)->withQueryString();
        $roomTypes = RoomType::all();
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
}
