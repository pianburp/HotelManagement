<?php

namespace App\Console\Commands;

use App\Http\Requests\BookingRequest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestBookingValidation extends Command
{
    protected $signature = 'booking:test-validation {room_id} {check_in} {check_out}';
    protected $description = 'Test booking validation for room availability';

    public function handle()
    {
        $roomId = $this->argument('room_id');
        $checkIn = $this->argument('check_in');
        $checkOut = $this->argument('check_out');

        $this->info("Testing booking validation for Room ID: {$roomId}");
        $this->info("Check-in: {$checkIn}");
        $this->info("Check-out: {$checkOut}");
        $this->line('');

        // Find the room
        $room = Room::with('roomType')->find($roomId);
        if (!$room) {
            $this->error("Room with ID {$roomId} not found!");
            return 1;
        }

        $this->info("Room: {$room->room_number} - {$room->roomType->name}");
        $this->info("Room Status: {$room->status}");
        $this->line('');

        // Test basic availability
        $isAvailable = $room->isAvailable($checkIn, $checkOut);
        $this->info("Basic Availability Check: " . ($isAvailable ? 'AVAILABLE' : 'NOT AVAILABLE'));

        // Test validation rules
        $data = [
            'room_id' => $roomId,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 1,
            'payment_method' => 'credit_card',
            'terms' => true,
        ];

        // Create a mock request for validation
        $request = new Request($data);
        $bookingRequest = new BookingRequest();
        
        // Get validation rules
        $rules = $bookingRequest->rules();
        $messages = $bookingRequest->messages();
        
        // Run validation
        $validator = Validator::make($data, $rules, $messages);
        
        // Add custom validation
        $validator->after(function ($validator) use ($room, $checkIn, $checkOut) {
            $this->validateRoomAvailability($validator, $room, $checkIn, $checkOut);
        });

        if ($validator->fails()) {
            $this->error('Validation FAILED:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  - {$error}");
            }
            return 1;
        } else {
            $this->info('Validation PASSED: ✓');
        }

        // Show existing bookings for this room
        $this->line('');
        $this->info('Existing bookings for this room:');
        $bookings = $room->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderBy('check_in_date')
            ->get();

        if ($bookings->isEmpty()) {
            $this->line('  No existing bookings');
        } else {
            foreach ($bookings as $booking) {
                $this->line("  - {$booking->check_in_date} to {$booking->check_out_date} ({$booking->status})");
            }
        }

        return 0;
    }

    private function validateRoomAvailability($validator, $room, $checkIn, $checkOut)
    {
        // Check if room status is available
        if ($room->status !== 'available') {
            $validator->errors()->add('room_id', 'Selected room is not available for booking.');
            return;
        }

        // Check for date conflicts with existing bookings
        $hasConflict = $room->bookings()
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
                });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->exists();
        
        if ($hasConflict) {
            $conflictingBookings = $room->bookings()
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->where(function ($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                          ->orWhere(function ($q) use ($checkIn, $checkOut) {
                              $q->where('check_in_date', '<=', $checkIn)
                                ->where('check_out_date', '>=', $checkOut);
                          });
                    });
                })
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->get(['check_in_date', 'check_out_date']);

            $dateRanges = [];
            foreach ($conflictingBookings as $booking) {
                $start = Carbon::parse($booking->check_in_date)->format('M j, Y');
                $end = Carbon::parse($booking->check_out_date)->format('M j, Y');
                $dateRanges[] = "{$start} - {$end}";
            }
            
            $dateRangesStr = implode(', ', $dateRanges);
            $validator->errors()->add('check_in', 
                "The selected dates conflict with existing bookings. Unavailable periods: {$dateRangesStr}"
            );
        }
    }
}
