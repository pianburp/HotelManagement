<?php

namespace App\Models;

use App\Services\RoomAvailabilityCacheService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in_date',
        'check_out_date',
        'guests_count',
        'total_amount',
        'status',
        'special_requests',
        'booking_source',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        // Invalidate cache when booking status changes
        static::updated(function ($booking) {
            if ($booking->isDirty(['status', 'check_in_date', 'check_out_date', 'room_id'])) {
                app(RoomAvailabilityCacheService::class)->invalidateRoomCache($booking->room_id);
                
                // If room_id changed, also invalidate the old room
                if ($booking->isDirty('room_id') && $booking->getOriginal('room_id')) {
                    app(RoomAvailabilityCacheService::class)->invalidateRoomCache($booking->getOriginal('room_id'));
                }
            }
        });

        static::created(function ($booking) {
            app(RoomAvailabilityCacheService::class)->invalidateRoomCache($booking->room_id);
        });

        static::deleted(function ($booking) {
            app(RoomAvailabilityCacheService::class)->invalidateRoomCache($booking->room_id);
        });
    }

    /**
     * Accessor for check_in to maintain compatibility
     */
    public function getCheckInAttribute()
    {
        return $this->check_in_date;
    }

    /**
     * Accessor for check_out to maintain compatibility
     */
    public function getCheckOutAttribute()
    {
        return $this->check_out_date;
    }

    /**
     * Accessor for number_of_guests to maintain compatibility
     */
    public function getNumberOfGuestsAttribute()
    {
        return $this->guests_count;
    }

    /**
     * Get the user who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room that was booked.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the payments for this booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
