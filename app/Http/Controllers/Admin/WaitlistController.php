<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaitlistController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of waitlist entries.
     */
    public function index(Request $request)
    {
        $query = Waitlist::with(['roomType.translations']);

        // Apply filters
        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $waitlists = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total' => Waitlist::count(),
            'pending' => Waitlist::where('status', 'pending')->count(),
            'notified' => Waitlist::where('status', 'notified')->count(),
            'completed' => Waitlist::where('status', 'completed')->count(),
        ];

        $roomTypes = RoomType::with('translations')->get();

        return view('admin.waitlist.index', compact('waitlists', 'stats', 'roomTypes'));
    }

    /**
     * Notify a waitlist entry about available room.
     */
    public function notify(Request $request, Waitlist $waitlist)
    {
        $this->validate($request, [
            'message' => 'required|string|max:1000',
            'send_email' => 'boolean',
            'send_sms' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Update waitlist status
            $waitlist->update([
                'status' => 'notified',
                'notified_at' => now(),
                'notification_message' => $request->message,
            ]);

            // Here you would implement the actual notification sending
            // Email notification
            if ($request->boolean('send_email')) {
                // Send email notification
                // Mail::to($waitlist->guest_email)->send(new WaitlistNotification($waitlist, $request->message));
            }

            // SMS notification
            if ($request->boolean('send_sms')) {
                // Send SMS notification
                // SMS::send($waitlist->guest_phone, $request->message);
            }

            DB::commit();

            return back()->with('success', 'Guest has been notified successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error sending notification: ' . $e->getMessage());
        }
    }

    /**
     * Mark waitlist entry as completed.
     */
    public function complete(Waitlist $waitlist)
    {
        $waitlist->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Waitlist entry marked as completed.');
    }

    /**
     * Get waitlist details for AJAX requests.
     */
    public function details(Waitlist $waitlist)
    {
        return response()->json([
            'guest_name' => $waitlist->guest_name,
            'guest_email' => $waitlist->guest_email,
            'guest_phone' => $waitlist->guest_phone,
            'number_of_guests' => $waitlist->number_of_guests,
            'preferred_check_in' => $waitlist->preferred_check_in->format('M d, Y'),
            'preferred_check_out' => $waitlist->preferred_check_out->format('M d, Y'),
            'room_type' => $waitlist->roomType->name,
            'priority' => ucfirst($waitlist->priority),
            'status' => ucfirst($waitlist->status),
            'special_requests' => $waitlist->special_requests,
            'created_at' => $waitlist->created_at->format('M d, Y H:i'),
        ]);
    }

    /**
     * Remove the specified waitlist entry.
     */
    public function destroy(Waitlist $waitlist)
    {
        try {
            $waitlist->delete();

            return back()->with('success', 'Waitlist entry removed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error removing waitlist entry: ' . $e->getMessage());
        }
    }

    /**
     * Bulk notify waitlist entries for a specific room type.
     */
    public function bulkNotify(Request $request)
    {
        $this->validate($request, [
            'room_type_id' => 'required|exists:room_types,id',
            'message' => 'required|string|max:1000',
            'limit' => 'integer|min:1|max:50',
        ]);

        $waitlists = Waitlist::where('room_type_id', $request->room_type_id)
                            ->where('status', 'pending')
                            ->orderBy('created_at', 'asc')
                            ->limit($request->limit ?? 10)
                            ->get();

        $notifiedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($waitlists as $waitlist) {
                $waitlist->update([
                    'status' => 'notified',
                    'notified_at' => now(),
                    'notification_message' => $request->message,
                ]);

                // Send notifications here
                $notifiedCount++;
            }

            DB::commit();

            return back()->with('success', "Successfully notified {$notifiedCount} guests.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error sending bulk notifications: ' . $e->getMessage());
        }
    }

    /**
     * Auto-notify waitlist entries when rooms become available.
     */
    public function autoNotify(Room $room)
    {
        if ($room->status !== 'available') {
            return;
        }

        $waitlists = Waitlist::where('room_type_id', $room->room_type_id)
                            ->where('status', 'pending')
                            ->where('preferred_check_in', '<=', now()->addDays(30))
                            ->orderBy('created_at', 'asc')
                            ->limit(5)
                            ->get();

        foreach ($waitlists as $waitlist) {
            $this->notify(
                new Request([
                    'message' => "Good news! A {$room->roomType->name} room is now available. Please contact us to book.",
                    'send_email' => true,
                    'send_sms' => false,
                ]), 
                $waitlist
            );
        }
    }
}
