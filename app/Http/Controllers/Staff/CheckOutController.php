<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckOutController extends Controller
{
    /**
     * Display a listing of check-outs.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $checkOuts = Booking::with(['user', 'room.roomType'])
            ->where('check_out_date', $date)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderBy('check_out_date')
            ->get();

        return view('staff.checkout.index', compact('checkOuts', 'date'));
    }

    /**
     * Show the form for processing check-out.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'room.roomType', 'payments']);

        // Verify this is a valid check-out
        if ($booking->check_out_date->isAfter(Carbon::today()) || 
            !in_array($booking->status, ['confirmed', 'checked_in'])) {
            return redirect()->route('staff.checkout.index')
                ->with('error', 'This booking is not ready for check-out.');
        }

        return view('staff.checkout.show', compact('booking'));
    }

    /**
     * Process the check-out.
     */
    public function process(Request $request, Booking $booking)
    {
        $request->validate([
            'damages' => 'nullable|string|max:500',
            'additional_charges' => 'nullable|numeric|min:0',
            'room_condition' => 'required|in:good,needs_cleaning,needs_maintenance',
            'notes' => 'nullable|string|max:500',
        ]);

        // Update booking status
        $updateData = ['status' => 'completed'];
        
        if ($request->additional_charges > 0) {
            $updateData['total_amount'] = $booking->total_amount + $request->additional_charges;
        }

        if ($request->notes || $request->damages) {
            $additionalNotes = '';
            if ($request->damages) {
                $additionalNotes .= "\n\nDamages: " . $request->damages;
            }
            if ($request->notes) {
                $additionalNotes .= "\n\nCheck-out Notes: " . $request->notes;
            }
            $updateData['special_requests'] = $booking->special_requests . $additionalNotes;
        }

        $booking->update($updateData);

        // Update room status based on condition
        $roomStatus = match($request->room_condition) {
            'good' => 'available',
            'needs_cleaning' => 'reserved',
            'needs_maintenance' => 'closed',
            default => 'reserved'
        };

        $booking->room->update(['status' => $roomStatus]);

        // Create status history
        $reason = 'Guest checked out - Booking #' . $booking->booking_reference;
        if ($request->room_condition !== 'good') {
            $reason .= ' (' . str_replace('_', ' ', $request->room_condition) . ')';
        }

        $booking->room->statusHistory()->create([
            'status' => $roomStatus,
            'reason' => $reason,
            'changed_by' => auth()->id(),
        ]);

        return redirect()->route('staff.checkout.index')
            ->with('success', 'Guest checked out successfully!');
    }
}
