<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use Carbon\Carbon;

echo "Checking bookings after seeder fix:\n";
echo "==================================\n";

$allBookings = Booking::with(['user', 'room'])->get();
echo "Total bookings in database: " . $allBookings->count() . "\n\n";

foreach($allBookings as $booking) {
    echo "ID: {$booking->id} | Ref: {$booking->booking_reference} | Room: {$booking->room->room_number} | Check-in: {$booking->check_in_date} | Status: {$booking->status}\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Today's check-ins (confirmed + room reserved):\n";

$todayCheckIns = Booking::with(['user', 'room'])
    ->where('check_in_date', Carbon::today())
    ->where('status', 'confirmed')
    ->whereHas('room', function ($query) {
        $query->where('status', 'reserved');
    })
    ->get();

echo "Count: " . $todayCheckIns->count() . "\n";
foreach($todayCheckIns as $booking) {
    echo "ID: {$booking->id} | Ref: {$booking->booking_reference} | Room: {$booking->room->room_number} | Room Status: {$booking->room->status}\n";
}
