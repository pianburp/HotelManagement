<?php

namespace App\Models;

use App\Services\RoomAvailabilityCacheService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class RoomType extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTranslations;

    public $translatable = [
        'name',
        'description',
        'size',
        'amenities_description',
    ];

    protected $fillable = [
        'code',
        'base_price',
        'max_occupancy',
        'amenities',
        'is_active',
        'name',
        'description',
        'size',
        'amenities_description',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
    ];

    protected static function booted()
    {
        // Invalidate cache when room type changes
        static::updated(function ($roomType) {
            if (
                $roomType->isDirty(['base_price', 'max_occupancy', 'amenities', 'is_active']) ||
                collect($roomType->translatable)->some(fn($field) => $roomType->isDirty($field))
            ) {
                app(RoomAvailabilityCacheService::class)->invalidateAllRoomCache();
            }
        });

        static::created(function () {
            app(RoomAvailabilityCacheService::class)->invalidateAllRoomCache();
        });

        static::deleted(function () {
            app(RoomAvailabilityCacheService::class)->invalidateAllRoomCache();
        });
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
     * For backwards compatibility - gets translations via relationship if needed
     * This allows us to transition smoothly to the Translatable trait
     */
    public function translations()
    {
        return $this->hasMany(RoomTypeTranslation::class);
    }
    
    /**
     * Override setTranslation method to update both the Spatie JSON field
     * and the traditional translation records for backwards compatibility
     */
    public function setTranslation($key, $locale, $value)
    {
        // Use the parent setTranslation method from Spatie Translatable
        parent::setTranslation($key, $locale, $value);
        
        // Also update the old translation model if it exists
        if (in_array($key, ['name', 'description', 'size', 'amenities_description'])) {
            $this->translations()->updateOrCreate(
                ['locale' => $locale],
                [$key => $value]
            );
        }
        
        return $this;
    }

    /**
     * Register media collections for room type images
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
    }

    /**
     * Register media conversions for different image sizes
     */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(400)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();
    }
}