<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use App\Models\Room;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    /**
     * Display a listing of waitlist entries.
     */
    public function index(Request $request)
    {
        $query = Waitlist::with(['user', 'roomType'])
            ->where('status', 'active');

        // Filter by room type
        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->where('check_in_date', '>=', $request->date_from);
        }

        $waitlists = $query->orderBy('check_in_date')->paginate(20);

        $roomTypes = \App\Models\RoomType::all();

        return view('staff.waitlist.index', compact('waitlists', 'roomTypes'));
    }

    /**
     * Notify a waitlist entry about available room.
     */
    public function notify(Request $request, Waitlist $waitlist)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'message' => 'nullable|string|max:500',
        ]);

        $room = Room::with('roomType')->findOrFail($request->room_id);

        // Update waitlist status
        $waitlist->update(['status' => 'notified']);

        // Here you would typically send an email/SMS notification
        // For now, we'll just show a success message

        return redirect()->route('staff.waitlist.index')
            ->with('success', "Notification sent to {$waitlist->user->name} about Room {$room->room_number}.");
    }
}
