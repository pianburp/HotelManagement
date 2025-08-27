<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'base_price',
        'max_occupancy',
        'amenities',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
    ];

    /**
     * Get the translations for the room type.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(RoomTypeTranslation::class);
    }

    /**
     * Get the rooms of this type.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get waitlist entries for this room type.
     */
    public function waitlist(): HasMany
    {
        return $this->hasMany(Waitlist::class);
    }

    /**
     * Get translation for specific locale.
     */
    public function getTranslation(string $locale = null): ?RoomTypeTranslation
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }
}
