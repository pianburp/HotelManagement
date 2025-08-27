<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Waitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_type_id',
        'check_in_date',
        'check_out_date',
        'guests_count',
        'status',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    /**
     * Accessor for preferred_check_in to maintain compatibility
     */
    public function getPreferredCheckInAttribute()
    {
        return $this->check_in_date;
    }

    /**
     * Accessor for preferred_check_out to maintain compatibility
     */
    public function getPreferredCheckOutAttribute()
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
     * Get the user who is on the waitlist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room type being waited for.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}
