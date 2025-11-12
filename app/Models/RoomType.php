<?php

namespace App\Models;

use App\Services\RoomAvailabilityCacheService;
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

    protected static function booted()
    {
        // Invalidate cache when room type changes
        static::updated(function ($roomType) {
            if ($roomType->isDirty(['base_price', 'max_occupancy', 'amenities', 'is_active'])) {
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
     * Get the translations for this room type.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(RoomTypeTranslation::class);
    }

    /**
     * Name accessor for compatibility with existing views.
     */
    public function getNameAttribute(): string
    {
        return $this->getName();
    }

    /**
     * Get a translated field value for a specific locale.
     */
    public function getTranslatedField(string $field, string $locale = 'en'): ?string
    {
        if (!$this->relationLoaded('translations') || !$this->translations) {
            $this->load('translations');
        }
        
        return $this->translations?->where('locale', $locale)->first()?->{$field};
    }

    /**
     * Get the name for a specific locale.
     */
    public function getName(string $locale = 'en'): string
    {
        return $this->getTranslatedField('name', $locale) ?? $this->code;
    }

    /**
     * Get the description for a specific locale.
     */
    public function getDescription(string $locale = 'en'): ?string
    {
        return $this->getTranslatedField('description', $locale);
    }

    /**
     * Get a translation for a specific field and locale.
     * This method provides compatibility with the view layer.
     */
    public function getTranslation(string $field, string $locale = 'en'): string
    {
        $translation = $this->getTranslatedField($field, $locale);
        
        // Fallback to code if name translation is not found
        if ($field === 'name' && !$translation) {
            return $this->code;
        }
        
        return $translation ?? '';
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