<?php

namespace App\Http\Requests;

use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:credit_card,debit_card,bank_transfer',
            'terms' => 'required|accepted',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateRoomAvailability($validator);
            $this->validateGuestCapacity($validator);
        });
    }

    /**
     * Validate that the room is available for the selected dates.
     */
    protected function validateRoomAvailability(Validator $validator): void
    {
        $roomId = $this->input('room_id');
        $checkIn = $this->input('check_in');
        $checkOut = $this->input('check_out');

        if (!$roomId || !$checkIn || !$checkOut) {
            return;
        }

        $room = Room::find($roomId);
        if (!$room) {
            $validator->errors()->add('room_id', 'Selected room not found.');
            return;
        }

        // Check if room status is available
        if ($room->status !== 'available') {
            $validator->errors()->add('room_id', 'Selected room is not available for booking.');
            return;
        }

        // Check for date conflicts with existing bookings
        $hasConflict = $this->checkDateConflicts($room, $checkIn, $checkOut);
        
        if ($hasConflict) {
            $conflictingDates = $this->getConflictingDateRanges($room, $checkIn, $checkOut);
            $dateRanges = implode(', ', $conflictingDates);
            
            $validator->errors()->add('check_in', 
                "The selected dates conflict with existing bookings. Unavailable periods: {$dateRanges}"
            );
        }
    }

    /**
     * Validate that the number of guests doesn't exceed room capacity.
     */
    protected function validateGuestCapacity(Validator $validator): void
    {
        $roomId = $this->input('room_id');
        $guests = $this->input('guests');

        if (!$roomId || !$guests) {
            return;
        }

        $room = Room::with('roomType')->find($roomId);
        if (!$room) {
            return;
        }

        if ($guests > $room->roomType->max_occupancy) {
            $validator->errors()->add('guests', 
                "Number of guests ({$guests}) exceeds room capacity ({$room->roomType->max_occupancy})."
            );
        }
    }

    /**
     * Check if the requested dates conflict with existing bookings.
     */
    protected function checkDateConflicts(Room $room, string $checkIn, string $checkOut): bool
    {
        return $room->bookings()
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    // Booking starts during our requested stay
                    $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                      // Booking ends during our requested stay  
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      // Booking encompasses our entire requested stay
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
                });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->exists();
    }

    /**
     * Get specific date ranges that conflict with the requested booking.
     */
    protected function getConflictingDateRanges(Room $room, string $checkIn, string $checkOut): array
    {
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
            ->select('check_in_date', 'check_out_date')
            ->get();

        $dateRanges = [];
        foreach ($conflictingBookings as $booking) {
            $start = Carbon::parse($booking->check_in_date)->format('M j, Y');
            $end = Carbon::parse($booking->check_out_date)->format('M j, Y');
            $dateRanges[] = "{$start} - {$end}";
        }

        return $dateRanges;
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'room_id.required' => 'Please select a room.',
            'room_id.exists' => 'Selected room is invalid.',
            'check_in.required' => 'Check-in date is required.',
            'check_in.date' => 'Check-in date must be a valid date.',
            'check_in.after' => 'Check-in date must be after today.',
            'check_out.required' => 'Check-out date is required.',
            'check_out.date' => 'Check-out date must be a valid date.',
            'check_out.after' => 'Check-out date must be after check-in date.',
            'guests.required' => 'Number of guests is required.',
            'guests.integer' => 'Number of guests must be a number.',
            'guests.min' => 'At least one guest is required.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Selected payment method is invalid.',
            'terms.required' => 'You must agree to the terms and conditions.',
            'terms.accepted' => 'You must agree to the terms and conditions.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'room_id' => 'room',
            'check_in' => 'check-in date',
            'check_out' => 'check-out date',
            'guests' => 'number of guests',
            'payment_method' => 'payment method',
            'terms' => 'terms and conditions',
        ];
    }
}
