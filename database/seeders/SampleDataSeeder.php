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
        // Clear existing sample data to prevent duplicates
        // Need to delete in correct order due to foreign key constraints
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('payments')->truncate();
        \DB::table('bookings')->truncate();
        \DB::table('waitlists')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get users
        $admin = User::where('email', 'admin@hotel.com')->first();
        $staff = User::where('email', 'staff@hotel.com')->first();
        $user = User::where('email', 'user@example.com')->first();

        // Get rooms and reset their status to available
        $rooms = Room::limit(5)->get();
        Room::query()->update(['status' => 'available']);

        if ($rooms->count() > 0) {
            // Create some sample bookings with deterministic references
            
            // Future booking
            Booking::create([
                'booking_reference' => 'BK5001',
                'user_id' => $user->id,
                'room_id' => $rooms[0]->id,
                'check_in_date' => Carbon::today()->addDays(3),
                'check_out_date' => Carbon::today()->addDays(6),
                'guests_count' => 2,
                'total_amount' => 450.00,
                'status' => 'confirmed',
            ]);

            // Past booking (completed)
            Booking::create([
                'booking_reference' => 'BK5002',
                'user_id' => $user->id,
                'room_id' => $rooms[1]->id,
                'check_in_date' => Carbon::today()->subDays(2),
                'check_out_date' => Carbon::yesterday(),
                'guests_count' => 1,
                'total_amount' => 300.00,
                'status' => 'completed',
            ]);

            // Today's check-in (this should be the ONLY booking for today)
            $todayBooking = Booking::create([
                'booking_reference' => 'BK5003',
                'user_id' => $user->id,
                'room_id' => $rooms[2]->id,
                'check_in_date' => Carbon::today(),
                'check_out_date' => Carbon::today()->addDays(2),
                'guests_count' => 2,
                'total_amount' => 400.00,
                'status' => 'confirmed',
            ]);

            // Currently checked in guest (checking out today)
            Booking::create([
                'booking_reference' => 'BK5004',
                'user_id' => $user->id,
                'room_id' => $rooms[3]->id,
                'check_in_date' => Carbon::yesterday(),
                'check_out_date' => Carbon::today(),
                'guests_count' => 3,
                'total_amount' => 350.00,
                'status' => 'checked_in',
            ]);

            // Update room statuses based on bookings
            $rooms[0]->update(['status' => 'available']); // Future booking
            $rooms[1]->update(['status' => 'available']); // Past booking completed
            $rooms[2]->update(['status' => 'reserved']);  // Today's check-in
            $rooms[3]->update(['status' => 'onboard']);   // Currently occupied
            
            // Mark one room as out of service
            if ($rooms->count() > 4) {
                $rooms[4]->update(['status' => 'closed']);
            }
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

        $this->command->info('Sample data created successfully:');
        $this->command->info('- 4 bookings created (1 for today\'s check-in)');
        $this->command->info('- Room statuses updated appropriately');
        $this->command->info('- 2 waitlist entries created');
    }
}
