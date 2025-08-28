<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\Waitlist;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Assign default role if user has no roles
        if (!$user->hasAnyRole(['admin', 'staff', 'user'])) {
            $user->assignRole('user');
        }
        
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('staff')) {
            return $this->staffDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::where('status', 'confirmed')
                ->where('check_in_date', '<=', now())
                ->where('check_out_date', '>=', now())
                ->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_revenue' => Booking::where('status', 'confirmed')->sum('total_amount'),
            'waitlist_count' => Waitlist::where('status', 'active')->count(),
        ];

        $recentBookings = Booking::with(['user', 'room.roomType'])
            ->latest()
            ->limit(5)
            ->get();

        $upcomingCheckIns = Booking::with(['user', 'room.roomType'])
            ->where('check_in_date', Carbon::today())
            ->where('status', 'confirmed')
            ->get();

        return view('dashboards.admin', compact('stats', 'recentBookings', 'upcomingCheckIns'));
    }

    private function staffDashboard()
    {
        $stats = [
            'available_rooms' => Room::where('status', 'available')->count(),
            'occupied_rooms' => Room::where('status', 'onboard')->count(),
            'reserved_rooms' => Room::where('status', 'reserved')->count(),
            'out_of_service' => Room::where('status', 'closed')->count(),
            // Show number of guests (sum of guests_count) for today's check-ins/check-outs so the UI label "guests" matches the value.
            'todays_checkins' => Booking::whereDate('check_in_date', Carbon::today())
                ->where('status', 'confirmed')
                ->sum('guests_count'),
            'todays_checkouts' => Booking::whereDate('check_out_date', Carbon::today())
                ->where('status', 'confirmed')
                ->sum('guests_count'),
            'active_waitlist' => Waitlist::where('status', 'active')->count(),
        ];

        $todaysCheckIns = Booking::with(['user', 'room.roomType'])
            ->whereDate('check_in_date', Carbon::today())
            ->where('status', 'confirmed')
            ->get();

        $todaysCheckOuts = Booking::with(['user', 'room.roomType'])
            ->whereDate('check_out_date', Carbon::today())
            ->where('status', 'confirmed')
            ->get();

        // The rooms table enum currently contains: available, reserved, onboard, closed
        // 'maintenance' is not present in the migration enum; limit to existing values.
        $roomsNeedingAttention = Room::whereIn('status', ['closed'])
            ->with('roomType')
            ->get();

        return view('dashboards.staff', compact('stats', 'todaysCheckIns', 'todaysCheckOuts', 'roomsNeedingAttention'));
    }

    private function userDashboard()
    {
        $user = auth()->user();
        
        $upcomingBooking = Booking::with(['room.roomType'])
            ->where('user_id', $user->id)
            ->where('check_in_date', '>', now())
            ->where('status', 'confirmed')
            ->orderBy('check_in_date')
            ->first();

        $recentBookings = Booking::with(['room.roomType'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(3)
            ->get();

        $activeWaitlist = Waitlist::with(['roomType'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        $stats = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'upcoming_bookings' => Booking::where('user_id', $user->id)
                ->where('check_in_date', '>', now())
                ->where('status', 'confirmed')
                ->count(),
            'active_waitlist' => $activeWaitlist->count(),
        ];

        return view('dashboards.user', compact('upcomingBooking', 'recentBookings', 'activeWaitlist', 'stats'));
    }
}
