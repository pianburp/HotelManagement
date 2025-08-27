<?php

namespace App\Http\Controllers\Admin;

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
        $query = Waitlist::with(['user', 'roomType']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by room type
        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        $waitlists = $query->latest()->paginate(20);

        $roomTypes = \App\Models\RoomType::all();
        $statuses = ['active', 'notified', 'expired'];

        return view('admin.waitlist.index', compact('waitlists', 'roomTypes', 'statuses'));
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

        return redirect()->route('admin.waitlist.index')
            ->with('success', "Notification sent to {$waitlist->user->name} about Room {$room->room_number}.");
    }

    /**
     * Remove the specified waitlist entry.
     */
    public function destroy(Waitlist $waitlist)
    {
        $waitlist->delete();

        return redirect()->route('admin.waitlist.index')
            ->with('success', 'Waitlist entry removed successfully!');
    }
}
