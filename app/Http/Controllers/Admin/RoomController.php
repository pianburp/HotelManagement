<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Cache::tags(['rooms'])->remember('rooms_list', 3600, function () {
            return Room::with(['roomType', 'roomType.translations'])->get();
        });

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roomTypes = RoomType::with('translations')->where('is_active', true)->get();
        return view('admin.rooms.create', compact('roomTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'room_number' => 'required|string|max:20|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'floor_number' => 'required|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'smoking_allowed' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            Room::create([
                'room_number' => $request->room_number,
                'room_type_id' => $request->room_type_id,
                'floor_number' => $request->floor_number,
                'size' => $request->size,
                'smoking_allowed' => $request->smoking_allowed ?? false,
                'status' => 'available',
                'notes' => $request->notes,
            ]);

            Cache::tags(['rooms'])->flush();

            return redirect()->route('admin.rooms.index')
                           ->with('success', 'Room created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating room: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $room->load(['roomType', 'roomType.translations', 'statusHistory']);
        return view('admin.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        $roomTypes = RoomType::with('translations')->where('is_active', true)->get();
        return view('admin.rooms.edit', compact('room', 'roomTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $this->validate($request, [
            'room_number' => 'required|string|max:20|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor_number' => 'required|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'smoking_allowed' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            $oldStatus = $room->status;
            $room->update([
                'room_number' => $request->room_number,
                'room_type_id' => $request->room_type_id,
                'floor_number' => $request->floor_number,
                'size' => $request->size,
                'smoking_allowed' => $request->smoking_allowed ?? false,
                'notes' => $request->notes,
            ]);

            if ($request->has('status') && $request->status !== $oldStatus) {
                $room->statusHistory()->create([
                    'previous_status' => $oldStatus,
                    'new_status' => $request->status,
                    'changed_by' => auth()->id(),
                    'reason' => $request->status_change_reason,
                    'created_at' => now(),
                ]);

                $room->update(['status' => $request->status]);
            }

            Cache::tags(['rooms'])->flush();

            return redirect()->route('admin.rooms.index')
                           ->with('success', 'Room updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating room: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        if ($room->bookings()->exists()) {
            return back()->with('error', 'Cannot delete room with existing bookings.');
        }

        try {
            $room->delete();
            Cache::tags(['rooms'])->flush();
            
            return redirect()->route('admin.rooms.index')
                           ->with('success', 'Room deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting room: ' . $e->getMessage());
        }
    }

    /**
     * Update room status.
     */
    public function updateStatus(Request $request, Room $room)
    {
        $this->validate($request, [
            'status' => 'required|in:available,reserved,onboard,closed',
            'reason' => 'required|string',
        ]);

        try {
            $oldStatus = $room->status;
            
            DB::transaction(function () use ($room, $request, $oldStatus) {
                $room->update(['status' => $request->status]);
                
                $room->statusHistory()->create([
                    'previous_status' => $oldStatus,
                    'new_status' => $request->status,
                    'changed_by' => auth()->id(),
                    'reason' => $request->reason,
                    'created_at' => now(),
                ]);
            });

            Cache::tags(['rooms'])->flush();

            return back()->with('success', 'Room status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating room status: ' . $e->getMessage());
        }
    }

}
