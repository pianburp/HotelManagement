<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms for staff management.
     */
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'statusHistory' => function ($q) {
            $q->latest()->limit(1);
        }]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by room type
        if ($request->filled('room_type')) {
            $query->whereHas('roomType', function ($q) use ($request) {
                $q->where('code', $request->room_type);
            });
        }

        $rooms = $query->orderBy('room_number')->paginate(20);

        $roomTypes = \App\Models\RoomType::all();
        $statuses = ['available', 'reserved', 'onboard', 'closed'];

        return view('staff.rooms.index', compact('rooms', 'roomTypes', 'statuses'));
    }

    /**
     * Show the form for editing room status.
     */
    public function edit(Room $room)
    {
        $room->load(['roomType', 'statusHistory' => function ($q) {
            $q->latest()->limit(5);
        }]);

        $statuses = [
            'available' => 'Available',
            'reserved' => 'Reserved',
            'onboard' => 'Occupied',
            'closed' => 'Out of Service'
        ];

        return view('staff.rooms.edit', compact('room', 'statuses'));
    }

    /**
     * Update room status.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,reserved,onboard,closed',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $room->status;
        
        // Update room status
        $room->update(['status' => $request->status]);

        // Create status history
        $room->statusHistory()->create([
            'status' => $request->status,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'changed_by' => auth()->id(),
        ]);

        $statusName = match($request->status) {
            'available' => 'Available',
            'reserved' => 'Reserved',
            'onboard' => 'Occupied',
            'closed' => 'Out of Service',
        };

        return redirect()->route('staff.rooms.index')
            ->with('success', "Room {$room->room_number} status updated to {$statusName}.");
    }
}
