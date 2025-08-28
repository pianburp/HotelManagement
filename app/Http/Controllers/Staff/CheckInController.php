<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    /**
     * Display a listing of check-ins.
     * 
     * Note: Currently showing duplicate bookings due to data integrity issue
     * where the same room has multiple confirmed bookings for the same date.
     * This should be resolved by adding database constraints to prevent
     * overlapping bookings for the same room.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $checkIns = Booking::with(['user', 'room.roomType'])
            ->where('check_in_date', $date)
            ->where('status', 'confirmed')
            ->whereHas('room', function ($query) {
                $query->where('status', 'reserved');
            })
            ->orderBy('check_in_date')
            ->orderBy('id') // Add consistent ordering to ensure deterministic results
            ->get();

        return view('staff.checkin.index', compact('checkIns', 'date'));
    }

    /**
     * Show the form for processing check-in.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'room.roomType']);

        // Verify this is a valid check-in
        if ($booking->check_in_date->isAfter(Carbon::today()) || $booking->status !== 'confirmed') {
            return redirect()->route('staff.checkin.index')
                ->with('error', 'This booking is not ready for check-in.');
        }

        return view('staff.checkin.show', compact('booking'));
    }

    /**
     * Process the check-in.
     */
    public function process(Request $request, Booking $booking)
    {
        $request->validate([
            'actual_guests' => 'required|integer|min:1|max:' . $booking->room->roomType->max_occupancy,
            'notes' => 'nullable|string|max:500',
            'id_verified' => 'required|boolean',
            'payment_confirmed' => 'required|boolean',
        ]);

        // Update booking status to checked_in
        $booking->update([
            'status' => 'checked_in',
            'special_requests' => $booking->special_requests . 
                ($request->notes ? "\n\nCheck-in Notes: " . $request->notes : '')
        ]);

        // Update room status to onboard
        $booking->room->update(['status' => 'onboard']);

        // Create room status history
        if (method_exists($booking->room, 'statusHistory')) {
            $booking->room->statusHistory()->create([
                'new_status' => 'onboard',
                'old_status' => $booking->room->getOriginal('status'),
                'reason' => 'Guest checked in - Booking #' . $booking->booking_reference,
                'changed_by' => auth()->id(),
            ]);
        }

        return redirect()->route('staff.checkin.index')
            ->with('success', 'Guest checked in successfully!');
    }
}
