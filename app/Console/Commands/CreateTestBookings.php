<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;

class CreateTestBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test bookings for occupancy report testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test bookings...');

        // Get some rooms
        $rooms = Room::take(5)->get();
        
        if ($rooms->isEmpty()) {
            $this->error('No rooms found. Please ensure rooms are seeded first.');
            return;
        }

        // Get a user for bookings
        $user = User::first();
        if (!$user) {
            $this->error('No users found. Please ensure users are seeded first.');
            return;
        }

        $today = Carbon::today();
        
        // Create various types of bookings
        $bookingData = [
            [
                'status' => 'checked_in',
                'check_in_date' => $today->copy()->subDays(2),
                'check_out_date' => $today->copy()->addDays(3),
                'guest_name' => 'John Doe',
                'guest_email' => 'john.doe@example.com',
                'guest_phone' => '+1234567890',
            ],
            [
                'status' => 'confirmed',
                'check_in_date' => $today->copy(),
                'check_out_date' => $today->copy()->addDays(2),
                'guest_name' => 'Jane Smith',
                'guest_email' => 'jane.smith@example.com',
                'guest_phone' => '+1234567891',
            ],
            [
                'status' => 'checked_in',
                'check_in_date' => $today->copy()->subDay(),
                'check_out_date' => $today->copy()->addDays(4),
                'guest_name' => 'Bob Wilson',
                'guest_email' => 'bob.wilson@example.com',
                'guest_phone' => '+1234567892',
            ],
        ];

        foreach ($bookingData as $index => $data) {
            if (isset($rooms[$index])) {
                $room = $rooms[$index];
                
                // Update room status based on booking status
                if ($data['status'] === 'checked_in') {
                    $room->update(['status' => 'onboard']);
                } elseif ($data['status'] === 'confirmed') {
                    $room->update(['status' => 'reserved']);
                }

                Booking::create([
                    'booking_reference' => 'TEST' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'guest_name' => $data['guest_name'],
                    'guest_email' => $data['guest_email'],
                    'guest_phone' => $data['guest_phone'],
                    'check_in_date' => $data['check_in_date'],
                    'check_out_date' => $data['check_out_date'],
                    'guests_count' => rand(1, 4),
                    'total_amount' => rand(200, 800),
                    'status' => $data['status'],
                    'special_requests' => 'Test booking - ' . $data['guest_name'],
                    'booking_source' => 'website',
                ]);

                $this->info("Created booking for room {$room->room_number} - Guest: {$data['guest_name']} - Status: {$data['status']}");
            }
        }

        $this->info('Test bookings created successfully!');
    }
}
