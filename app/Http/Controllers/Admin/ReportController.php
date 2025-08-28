<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display the main reports dashboard.
     */
    public function index()
    {
        // Get basic statistics
        $stats = [
            'total_rooms' => Room::count(),
            'current_occupancy' => $this->getCurrentOccupancyRate(),
            'monthly_revenue' => Payment::where('payment_status', 'completed')
                                      ->whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->sum('amount'),
            'avg_occupancy' => $this->getAverageOccupancyRate(),
        ];

        // Get room type performance
        $roomTypeStats = $this->getRoomTypePerformance();

        // Get recent activity
        $recentActivity = $this->getRecentActivity();

        // Get chart data
        $occupancyChartData = $this->getOccupancyChartData();
        $revenueChartData = $this->getRevenueChartData();

        // Get top performing rooms
        $topRooms = $this->getTopPerformingRooms();

        return view('admin.reports.index', compact(
            'stats', 
            'roomTypeStats', 
            'recentActivity', 
            'occupancyChartData', 
            'revenueChartData',
            'topRooms'
        ));
    }

    /**
     * Display occupancy reports.
     */
    public function occupancy(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Get daily occupancy data
        $occupancyData = $this->getOccupancyData($startDate, $endDate);

        // Get room type occupancy
        $roomTypeOccupancy = $this->getRoomTypeOccupancy($startDate, $endDate);

        return view('admin.reports.occupancy', compact(
            'occupancyData', 
            'roomTypeOccupancy', 
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Display revenue reports.
     */
    public function revenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Get revenue data
        $revenueData = $this->getRevenueData($startDate, $endDate);

        // Get revenue by room type
        $revenueByRoomType = $this->getRevenueByRoomType($startDate, $endDate);

        // Get payment method breakdown
        $paymentMethodBreakdown = $this->getPaymentMethodBreakdown($startDate, $endDate);

        return view('admin.reports.revenue', compact(
            'revenueData', 
            'revenueByRoomType', 
            'paymentMethodBreakdown',
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Get current occupancy rate.
     */
    private function getCurrentOccupancyRate()
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'onboard')->count();
        
        return $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
    }

    /**
     * Get average occupancy rate for the last 30 days.
     */
    private function getAverageOccupancyRate()
    {
        $totalRooms = Room::count();
        if ($totalRooms == 0) return 0;

    $avgOccupied = Booking::where('status', 'checked_in')
                 ->whereDate('check_in_date', '>=', now()->subDays(30))
                 ->count() / 30;
        
        return round(($avgOccupied / $totalRooms) * 100, 1);
    }

    /**
     * Get room type performance data.
     */
    private function getRoomTypePerformance()
    {
        return RoomType::with('translations')
            ->withCount(['rooms'])
            ->get()
            ->map(function ($roomType) {
                $occupiedRooms = $roomType->rooms()->where('status', 'onboard')->count();
                $occupancyRate = $roomType->rooms_count > 0 
                    ? round(($occupiedRooms / $roomType->rooms_count) * 100, 1) 
                    : 0;

                $revenue = Payment::whereHas('booking.room', function ($query) use ($roomType) {
                    $query->where('room_type_id', $roomType->id);
                })
                ->where('payment_status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount');

                return [
                    'name' => $roomType->name,
                    'total_rooms' => $roomType->rooms_count,
                    'occupancy_rate' => $occupancyRate,
                    'revenue' => $revenue,
                ];
            });
    }

    /**
     * Get recent activity for the dashboard.
     */
    private function getRecentActivity()
    {
        $activities = collect();

        // Recent bookings
        $recentBookings = Booking::with(['room.roomType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'title' => 'New Booking',
                    'description' => $booking->guest_name . ' booked ' . $booking->room->roomType->name,
                    'time' => $booking->created_at->diffForHumans(),
                    'color' => 'bg-green-500',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
                ];
            });

        // Recent payments
        $recentPayments = Payment::with(['booking'])
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($payment) {
                return [
                    'title' => 'Payment Received',
                    'description' => '$' . number_format($payment->amount, 2) . ' from ' . $payment->booking->guest_name,
                    'time' => $payment->created_at->diffForHumans(),
                    'color' => 'bg-blue-500',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>',
                ];
            });

        return $activities->merge($recentBookings)->merge($recentPayments)->take(8);
    }

    /**
     * Get occupancy chart data for the last 30 days.
     */
    private function getOccupancyChartData()
    {
        $dates = collect();
        $occupancyRates = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates->push($date->format('M j'));

            $totalRooms = Room::count();
            $occupiedRooms = Booking::where('status', 'checked_in')
                ->whereDate('check_in_date', '<=', $date)
                ->whereDate('check_out_date', '>', $date)
                ->count();

            $rate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
            $occupancyRates->push($rate);
        }

        return [
            'labels' => $dates->toArray(),
            'data' => $occupancyRates->toArray(),
        ];
    }

    /**
     * Get revenue chart data for the last 12 months.
     */
    private function getRevenueChartData()
    {
        $months = collect();
        $revenues = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));

            $revenue = Payment::where('payment_status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $revenues->push((float) $revenue);
        }

        return [
            'labels' => $months->toArray(),
            'data' => $revenues->toArray(),
        ];
    }

    /**
     * Get top performing rooms for this month.
     */
    private function getTopPerformingRooms()
    {
        return Room::with(['roomType.translations'])
            ->select('rooms.*')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('SUM(payments.amount) as revenue')
            ->selectRaw('AVG(5) as avg_rating') // Placeholder for rating system
            ->leftJoin('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->leftJoin('payments', 'bookings.id', '=', 'payments.booking_id')
            ->whereMonth('bookings.created_at', now()->month)
            ->where('payments.payment_status', 'completed')
            ->groupBy('rooms.id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(function ($room) {
                $totalDays = now()->daysInMonth;
                $bookedDays = $room->bookings_count; // Simplified calculation
                $occupancyRate = min(100, round(($bookedDays / $totalDays) * 100, 1));

                return [
                    'room_number' => $room->room_number,
                    'room_type' => $room->roomType->name,
                    'bookings_count' => $room->bookings_count ?: 0,
                    'occupancy_rate' => $occupancyRate,
                    'revenue' => $room->revenue ?: 0,
                    'avg_rating' => $room->avg_rating ?: 5,
                ];
            });
    }

    /**
     * Get occupancy data for a date range.
     */
    private function getOccupancyData($startDate, $endDate)
    {
        // Implementation for detailed occupancy data
        return collect();
    }

    /**
     * Get room type occupancy for a date range.
     */
    private function getRoomTypeOccupancy($startDate, $endDate)
    {
        // Implementation for room type occupancy
        return collect();
    }

    /**
     * Get revenue data for a date range.
     */
    private function getRevenueData($startDate, $endDate)
    {
        // Implementation for revenue data
        return collect();
    }

    /**
     * Get revenue by room type for a date range.
     */
    private function getRevenueByRoomType($startDate, $endDate)
    {
        // Implementation for revenue by room type
        return collect();
    }

    /**
     * Get payment method breakdown for a date range.
     */
    private function getPaymentMethodBreakdown($startDate, $endDate)
    {
    return Payment::where('payment_status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->get();
    }
}
