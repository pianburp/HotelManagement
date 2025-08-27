<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRoomTypesSeeder extends Seeder
{
    public function run()
    {
        // Update room types with correct pricing
        DB::table('room_types')->where('code', 'STD')->update([
            'base_price' => 100.00,
            'max_occupancy' => 2,
            'amenities' => json_encode(['wifi', 'tv', 'ac']),
        ]);

        DB::table('room_types')->where('code', 'DLX')->update([
            'base_price' => 200.00,
            'max_occupancy' => 3,
            'amenities' => json_encode(['wifi', 'tv', 'ac', 'minibar', 'balcony']),
        ]);

        DB::table('room_types')->where('code', 'SUT')->update([
            'base_price' => 300.00,
            'max_occupancy' => 4,
            'amenities' => json_encode(['wifi', 'tv', 'ac', 'minibar', 'balcony', 'kitchen', 'jacuzzi']),
        ]);

        echo "Room types updated successfully!\n";
    }
}
