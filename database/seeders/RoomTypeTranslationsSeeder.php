<?php

namespace Database\Seeders;

use App\Models\RoomType;
use App\Models\RoomTypeTranslation;
use Illuminate\Database\Seeder;

class RoomTypeTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = RoomType::all();
        
        foreach ($roomTypes as $roomType) {
            // Skip if translation already exists
            if ($roomType->translations()->where('locale', 'en')->exists()) {
                continue;
            }
            
            // Create user-friendly names based on room type codes
            $friendlyNames = [
                'STD' => 'Standard Room',
                'DLX' => 'Deluxe Room',
                'STE' => 'Suite Room',
                'PRES' => 'Presidential Suite',
                'FAM' => 'Family Room',
            ];
            
            $name = $friendlyNames[$roomType->code] ?? 'Room Type ' . $roomType->code;
            
            RoomTypeTranslation::create([
                'room_type_id' => $roomType->id,
                'locale' => 'en',
                'name' => $name,
                'description' => 'Description for ' . $name,
                'size' => '25 sqm',
                'amenities_description' => 'Standard amenities for ' . $name
            ]);
        }
        
        $this->command->info('Created translations for ' . $roomTypes->count() . ' room types');
    }
}
