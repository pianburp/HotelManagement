<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Waitlist;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get users
        $admin = User::where('email', 'admin@hotel.com')->first();
        $staff = User::where('email', 'staff@hotel.com')->first();
        $user = User::where('email', 'user@example.com')->first();

        // Get rooms
        $rooms = Room::limit(5)->get();

        if ($rooms->count() > 0) {
            // Create some sample bookings
            Booking::create([
                'booking_reference' => 'BK' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'room_id' => $rooms[0]->id,
                'check_in_date' => Carbon::today()->addDays(3),
                'check_out_date' => Carbon::today()->addDays(6),
                'guests_count' => 2,
                'total_amount' => 450.00,
                'status' => 'confirmed',
            ]);

            Booking::create([
                'booking_reference' => 'BK' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'room_id' => $rooms[1]->id,
                'check_in_date' => Carbon::today()->subDays(2),
                'check_out_date' => Carbon::yesterday(),
                'guests_count' => 1,
                'total_amount' => 300.00,
                'status' => 'confirmed',
            ]);

            // Today's check-in
            Booking::create([
                'booking_reference' => 'BK' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'room_id' => $rooms[2]->id,
                'check_in_date' => Carbon::today(),
                'check_out_date' => Carbon::today()->addDays(2),
                'guests_count' => 2,
                'total_amount' => 400.00,
                'status' => 'confirmed',
            ]);

            // Today's check-out
            Booking::create([
                'booking_reference' => 'BK' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'room_id' => $rooms[3]->id,
                'check_in_date' => Carbon::yesterday(),
                'check_out_date' => Carbon::today(),
                'guests_count' => 3,
                'total_amount' => 350.00,
                'status' => 'confirmed',
            ]);

            // Update some room statuses for variety
            $rooms[1]->update(['status' => 'onboard']);
            $rooms[2]->update(['status' => 'reserved']);
            $rooms[3]->update(['status' => 'closed']);
        }

        // Create some waitlist entries
        Waitlist::create([
            'user_id' => $user->id,
            'room_type_id' => 1, // Standard room
            'check_in_date' => Carbon::today()->addDays(10),
            'check_out_date' => Carbon::today()->addDays(13),
            'guests_count' => 2,
            'status' => 'active',
        ]);

        Waitlist::create([
            'user_id' => $user->id,
            'room_type_id' => 2, // Deluxe room
            'check_in_date' => Carbon::today()->addDays(15),
            'check_out_date' => Carbon::today()->addDays(18),
            'guests_count' => 1,
            'status' => 'active',
        ]);
    }
}
