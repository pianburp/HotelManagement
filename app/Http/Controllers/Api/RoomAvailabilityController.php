<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomAvailabilityController extends Controller
{
    /**
     * Get available dates for a specific room.
     */
    public function getAvailableDates(Request $request, Room $room): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'months' => 'nullable|integer|min:1|max:12'
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now();
        $months = $request->months ?? 3;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate->copy()->addMonths($months);

        // Get all booked dates for this room
        $bookedDates = $this->getBookedDates($room, $startDate, $endDate);
        
        // Get all dates in the range
        $period = CarbonPeriod::create($startDate, $endDate);
        $allDates = [];
        
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $allDates[] = [
                'date' => $dateString,
                'available' => !in_array($dateString, $bookedDates) && 
                             $room->status === 'available' && 
                             $date->isFuture(),
                'day_name' => $date->format('l'),
                'is_weekend' => $date->isWeekend(),
            ];
        }

        return response()->json([
            'room_id' => $room->id,
            'room_number' => $room->room_number,
            'room_type' => $room->roomType->name,
            'dates' => $allDates,
            'booked_periods' => $this->getBookedPeriods($room, $startDate, $endDate),
        ]);
    }

    /**
     * Check if specific dates are available for a room.
     */
    public function checkAvailability(Request $request, Room $room): JsonResponse
    {
        $request->validate([
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        $isAvailable = $room->isAvailable($checkIn->format('Y-m-d'), $checkOut->format('Y-m-d'));
        
        $conflicts = [];
        if (!$isAvailable) {
            $conflicts = $this->getConflictingBookings($room, $checkIn, $checkOut);
        }

        return response()->json([
            'available' => $isAvailable,
            'room_id' => $room->id,
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'nights' => $checkIn->diffInDays($checkOut),
            'conflicts' => $conflicts,
            'room_status' => $room->status,
        ]);
    }

    /**
     * Get all booked dates for a room in a given period.
     */
    private function getBookedDates(Room $room, Carbon $startDate, Carbon $endDate): array
    {
        $bookings = $room->bookings()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_date', [$startDate, $endDate])
                      ->orWhereBetween('check_out_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('check_in_date', '<=', $startDate)
                            ->where('check_out_date', '>=', $endDate);
                      });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get(['check_in_date', 'check_out_date']);

        $bookedDates = [];
        foreach ($bookings as $booking) {
            $period = CarbonPeriod::create(
                Carbon::parse($booking->check_in_date),
                Carbon::parse($booking->check_out_date)->subDay() // Exclude checkout date
            );
            
            foreach ($period as $date) {
                $bookedDates[] = $date->format('Y-m-d');
            }
        }

        return array_unique($bookedDates);
    }

    /**
     * Get booked periods for display purposes.
     */
    private function getBookedPeriods(Room $room, Carbon $startDate, Carbon $endDate): array
    {
        $bookings = $room->bookings()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_date', [$startDate, $endDate])
                      ->orWhereBetween('check_out_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('check_in_date', '<=', $startDate)
                            ->where('check_out_date', '>=', $endDate);
                      });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get(['check_in_date', 'check_out_date', 'status']);

        return $bookings->map(function ($booking) {
            return [
                'check_in' => Carbon::parse($booking->check_in_date)->format('Y-m-d'),
                'check_out' => Carbon::parse($booking->check_out_date)->format('Y-m-d'),
                'status' => $booking->status,
            ];
        })->toArray();
    }

    /**
     * Get conflicting bookings for the requested period.
     */
    private function getConflictingBookings(Room $room, Carbon $checkIn, Carbon $checkOut): array
    {
        $conflicts = $room->bookings()
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
            ->get(['check_in_date', 'check_out_date', 'status']);

        return $conflicts->map(function ($booking) {
            return [
                'check_in' => Carbon::parse($booking->check_in_date)->format('M j, Y'),
                'check_out' => Carbon::parse($booking->check_out_date)->format('M j, Y'),
                'status' => $booking->status,
            ];
        })->toArray();
    }
}
