<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RoomType extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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

    /**
     * Get the name attribute (translated).
     */
    public function getNameAttribute(): ?string
    {
        $translation = $this->getTranslation();
        return $translation ? $translation->name : null;
    }

    /**
     * Get the description attribute (translated).
     */
    public function getDescriptionAttribute(): ?string
    {
        $translation = $this->getTranslation();
        return $translation ? $translation->description : null;
    }

    /**
     * Set translation for a specific locale.
     */
    public function setTranslation(string $attribute, string $locale, string $value): self
    {
        $translation = $this->getTranslation($locale);

        if ($translation) {
            $translation->update([$attribute => $value]);
        } else {
            $this->translations()->create([
                'locale' => $locale,
                $attribute => $value,
            ]);
        }

        return $this;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public');
    }
}
