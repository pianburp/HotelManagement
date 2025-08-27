<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // Create Admin
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@hotel.com',
            'password' => bcrypt('adminpassword'),
            'phone' => '+1234567890',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // Create Staff
        $staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@hotel.com',
            'password' => bcrypt('staffpassword'),
            'phone' => '+1234567891',
            'status' => 'active',
        ]);
        $staff->assignRole('staff');

        // Create Regular User
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('userpassword'),
            'phone' => '+1234567892',
            'status' => 'active',
        ]);
        $user->assignRole('user');

        // Create Room Types
        \DB::table('room_types')->insert([
            [
                'code' => 'STD',
                'base_price' => 100.00,
                'max_occupancy' => 2,
                'amenities' => json_encode(['wifi', 'tv', 'ac']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DLX',
                'base_price' => 200.00,
                'max_occupancy' => 3,
                'amenities' => json_encode(['wifi', 'tv', 'ac', 'minibar', 'balcony']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SUT',
                'base_price' => 300.00,
                'max_occupancy' => 4,
                'amenities' => json_encode(['wifi', 'tv', 'ac', 'minibar', 'balcony', 'kitchen', 'jacuzzi']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create Room Type Translations
        $translations = [
            'en' => [
                ['name' => 'Standard Room', 'description' => 'Comfortable standard room'],
                ['name' => 'Deluxe Room', 'description' => 'Spacious deluxe room with city view'],
                ['name' => 'Suite', 'description' => 'Luxury suite with all amenities'],
            ],
            'es' => [
                ['name' => 'Habitación Estándar', 'description' => 'Cómoda habitación estándar'],
                ['name' => 'Habitación Deluxe', 'description' => 'Amplia habitación deluxe con vista a la ciudad'],
                ['name' => 'Suite', 'description' => 'Suite de lujo con todas las comodidades'],
            ],
            'zh' => [
                ['name' => '标准房', 'description' => '舒适的标准客房'],
                ['name' => '豪华房', 'description' => '宽敞的豪华城景房'],
                ['name' => '套房', 'description' => '配备所有设施的豪华套房'],
            ],
        ];

        foreach ($translations as $locale => $rooms) {
            foreach ($rooms as $index => $room) {
                \DB::table('room_type_translations')->insert([
                    'room_type_id' => $index + 1,
                    'locale' => $locale,
                    'name' => $room['name'],
                    'description' => $room['description'],
                ]);
            }
        }

        // Create Rooms
        $roomTypes = [1 => 'STD', 2 => 'DLX', 3 => 'SUT'];
        foreach ($roomTypes as $typeId => $code) {
            for ($floor = 1; $floor <= 3; $floor++) {
                for ($room = 1; $room <= 3; $room++) {
                    \DB::table('rooms')->insert([
                        'room_number' => $floor . $code . str_pad($room, 2, '0', STR_PAD_LEFT),
                        'room_type_id' => $typeId,
                        'floor_number' => $floor,
                        'size' => rand(30, 100),
                        'smoking_allowed' => false,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Add sample data for testing
        $this->call(SampleDataSeeder::class);
    }
}
