<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the real-time occupancy report.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $today = Carbon::today();
        
        // Fetch all rooms with their current status and detailed booking information
        $rooms = Room::with([
            'roomType.translations', 
            'bookings' => function($query) use ($today) {
                $query->whereIn('status', ['confirmed', 'checked_in'])
                      ->where('check_in_date', '<=', $today)
                      ->where('check_out_date', '>=', $today);
            },
            'bookings.user' // Include user relationship for guest details
        ])->get();

        // Process room data for display with detailed guest information
        $occupancyData = $rooms->map(function ($room) use ($today) {
            $currentBooking = $room->bookings->first();
            
            // Determine actual room status based on booking
            $actualStatus = $room->status;
            if ($currentBooking) {
                if ($currentBooking->status === 'checked_in') {
                    $actualStatus = 'onboard'; // Guest is checked in
                } elseif ($currentBooking->status === 'confirmed') {
                    $actualStatus = 'reserved'; // Booking confirmed but not checked in yet
                }
            }
            // If no current booking, keep the original room status (available, closed, etc.)
            
            return [
                'room_number' => $room->room_number,
                'room_type' => $room->roomType->name ?? 'N/A',
                'status' => $actualStatus,
                'guest_name' => $currentBooking ? ($currentBooking->guest_name ?? $currentBooking->user->name ?? null) : null,
                'guest_email' => $currentBooking ? ($currentBooking->guest_email ?? $currentBooking->user->email ?? null) : null,
                'guest_phone' => $currentBooking ? ($currentBooking->guest_phone ?? $currentBooking->user->phone ?? null) : null,
                'guests_count' => $currentBooking ? $currentBooking->guests_count : null,
                'check_in_date' => $currentBooking ? $currentBooking->check_in_date->format('M d, Y') : null,
                'check_out_date' => $currentBooking ? $currentBooking->check_out_date->format('M d, Y') : null,
                'booking_reference' => $currentBooking ? $currentBooking->booking_reference : null,
                'special_requests' => $currentBooking ? $currentBooking->special_requests : null,
                'total_amount' => $currentBooking ? $currentBooking->total_amount : null,
                'booking_source' => $currentBooking ? $currentBooking->booking_source : null,
                'days_remaining' => $currentBooking ? $currentBooking->check_out_date->diffInDays($today) : null,
            ];
        });

        // Calculate statistics based on actual occupancy data
        $totalRooms = $occupancyData->count();
        $occupiedRooms = $occupancyData->where('status', 'onboard')->count();
        $availableRooms = $occupancyData->where('status', 'available')->count();
        $reservedRooms = $occupancyData->where('status', 'reserved')->count();
        $closedRooms = $occupancyData->where('status', 'closed')->count();
        
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        $statistics = [
            'occupied' => $occupiedRooms,
            'available' => $availableRooms,
            'reserved' => $reservedRooms,
            'closed' => $closedRooms,
            'occupancy_rate' => $occupancyRate,
            'total_rooms' => $totalRooms,
        ];

        return view('staff.reports.index', compact('occupancyData', 'statistics'));
    }
}