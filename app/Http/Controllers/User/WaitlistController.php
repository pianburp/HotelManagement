<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Waitlist;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $waitlistItems = Waitlist::with(['roomType.translations', 'roomType.media'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.waitlist.index', compact('waitlistItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $roomType = null;
        if ($request->filled('room_type_id')) {
            $roomType = RoomType::with('translations')->find($request->room_type_id);
        } elseif ($request->filled('room_type')) {
            $roomType = RoomType::with('translations')->find($request->room_type);
        }

        $roomTypes = RoomType::with('translations')->where('is_active', true)->get();

        return view('user.waitlist.create', compact('roomType', 'roomTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'preferred_check_in' => 'required|date|after:today',
            'preferred_check_out' => 'required|date|after:preferred_check_in',
            'number_of_guests' => 'required|integer|min:1',
            'max_price' => 'required|numeric|min:0',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        Waitlist::create([
            'user_id' => auth()->id(),
            'room_type_id' => $request->room_type_id,
            'preferred_check_in' => $request->preferred_check_in,
            'preferred_check_out' => $request->preferred_check_out,
            'number_of_guests' => $request->number_of_guests,
            'max_price' => $request->max_price,
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'special_requests' => $request->special_requests,
            'status' => 'active',
        ]);

        return redirect()->route('user.waitlist.index')
            ->with('success', 'You have been added to the waitlist successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Waitlist $waitlist)
    {
        // Ensure user can only delete their own waitlist entries
        if ($waitlist->user_id !== auth()->id()) {
            abort(403);
        }

        $waitlist->delete();

        return redirect()->route('user.waitlist.index')
            ->with('success', 'You have been removed from the waitlist.');
    }
}
