<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor_number',
        'size',
        'smoking_allowed',
        'status',
        'last_maintenance',
        'notes',
    ];

    protected $casts = [
        'smoking_allowed' => 'boolean',
        'size' => 'decimal:2',
        'last_maintenance' => 'date',
    ];

    /**
     * The room type this room belongs to.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get all bookings for this room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the current active booking for this room.
     */
    public function currentBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->whereDate('check_in_date', '<=', today())
            ->whereDate('check_out_date', '>=', today());
    }

    /**
     * Get the next upcoming booking for this room (future confirmed booking with earliest check-in).
     */
    public function upcomingBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->where('status', 'confirmed')
            ->whereDate('check_in_date', '>', today())
            ->oldestOfMany('check_in_date');
    }

    /**
     * Get status history for this room.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(RoomStatusHistory::class);
    }

    /**
     * Check if the room is available for booking.
     */
    public function isAvailable($checkIn = null, $checkOut = null): bool
    {
        // First check if room status is available
        if ($this->status !== 'available') {
            return false;
        }

        // If no dates provided, just check status
        if (!$checkIn || !$checkOut) {
            return true;
        }

        // Check if there are any confirmed bookings that overlap with the requested dates
        $hasConflictingBooking = $this->bookings()
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    // Booking starts during our stay
                    $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                      // Booking ends during our stay
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      // Booking encompasses our entire stay
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
                });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->exists();

        return !$hasConflictingBooking;
    }

    /**
     * Get the current availability status as a string.
     */
    public function getAvailabilityStatus(): string
    {
        return match($this->status) {
            'available' => 'Available',
            'reserved' => 'Reserved',
            'onboard' => 'Occupied',
            'closed' => 'Out of Service',
            default => 'Unknown'
        };
    }

    /**
     * Check if room is currently occupied.
     */
    public function isOccupied(): bool
    {
        return $this->status === 'onboard';
    }

    /**
     * Check if room is reserved.
     */
    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }

    /**
     * Check if room is out of service.
     */
    public function isOutOfService(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Accessor for backward compatibility with 'floor' attribute.
     */
    public function getFloorAttribute(): int
    {
        return $this->floor_number;
    }

    /**
     * Accessor for backward compatibility with 'is_smoking' attribute.
     */
    public function getIsSmokingAttribute(): bool
    {
        return $this->smoking_allowed;
    }
}
