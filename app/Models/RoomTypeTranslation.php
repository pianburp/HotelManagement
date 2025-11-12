<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomTypeTranslation extends Model
{
    protected $fillable = [
        'room_type_id',
        'locale',
        'name',
        'description',
        'size',
        'amenities_description',
    ];

    /**
     * Get the room type that this translation belongs to.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}
