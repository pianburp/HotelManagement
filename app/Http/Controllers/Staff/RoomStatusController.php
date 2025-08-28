<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\RoomStatusHistory;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(RoomStatusHistory $roomStatusHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomStatusHistory $roomStatusHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoomStatusHistory $roomStatusHistory)
    {
        $request->validate([
            'status' => 'required|in:available,reserved,onboard,closed',
            'room_id' => 'required|exists:rooms,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $room = Room::findOrFail($request->room_id);
        $oldStatus = $room->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            $room->status = $newStatus;
            $room->save();

            RoomStatusHistory::create([
                'room_id' => $room->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => auth()->id(),
                'reason' => $request->reason,
            ]);
        }

    return redirect()->route('staff.rooms.index')->with('success', __('Room status updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomStatusHistory $roomStatusHistory)
    {
        //
    }
}
