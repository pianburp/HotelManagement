<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\User;
use App\Services\RoomAvailabilityCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RedisCacheTest extends TestCase
{
    use RefreshDatabase;

    protected $cacheService;
    protected $room;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = app(RoomAvailabilityCacheService::class);
        
        // Create test data
        $roomType = RoomType::factory()->create([
            'base_price' => 100.00,
            'max_occupancy' => 2,
            'is_active' => true,
        ]);

        $this->room = Room::factory()->create([
            'room_type_id' => $roomType->id,
            'status' => 'available',
        ]);

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_cache_room_availability()
    {
        $checkIn = '2025-09-01';
        $checkOut = '2025-09-03';

        // First call should hit the database
        $isAvailable = $this->room->isAvailable($checkIn, $checkOut);
        $this->assertTrue($isAvailable);

        // Second call should hit the cache
        $cachedResult = $this->cacheService->getRoomAvailability($this->room->id, $checkIn, $checkOut);
        $this->assertTrue($cachedResult);
    }

    /** @test */
    public function it_invalidates_cache_when_booking_is_created()
    {
        $checkIn = '2025-09-01';
        $checkOut = '2025-09-03';

        // Cache the availability
        $this->room->isAvailable($checkIn, $checkOut);
        $this->assertNotNull($this->cacheService->getRoomAvailability($this->room->id, $checkIn, $checkOut));

        // Create a booking
        Booking::create([
            'booking_reference' => 'TEST123',
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'guest_name' => 'Test Guest',
            'guest_email' => 'test@example.com',
            'guest_phone' => '1234567890',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'guests_count' => 2,
            'total_amount' => 200.00,
            'status' => 'confirmed',
        ]);

        // Cache should be invalidated
        $this->assertNull($this->cacheService->getRoomAvailability($this->room->id, $checkIn, $checkOut));
    }

    /** @test */
    public function it_can_cache_search_results()
    {
        $searchParams = [
            'check_in' => '2025-09-01',
            'check_out' => '2025-09-03',
            'occupancy' => 2,
        ];

        $results = [$this->room->id];

        // Cache the search results
        $this->cacheService->cacheSearchResults($searchParams, $results);

        // Retrieve from cache
        $cachedResults = $this->cacheService->getSearchResults($searchParams);
        $this->assertEquals($results, $cachedResults);
    }

    /** @test */
    public function it_can_cache_room_statistics()
    {
        $stats = [
            'total' => 10,
            'available' => 8,
            'reserved' => 1,
            'occupied' => 1,
            'maintenance' => 0,
        ];

        $this->cacheService->cacheRoomStats($stats);
        $cachedStats = $this->cacheService->getRoomStats();

        $this->assertEquals($stats, $cachedStats);
    }

    /** @test */
    public function it_invalidates_cache_when_room_status_changes()
    {
        $checkIn = '2025-09-01';
        $checkOut = '2025-09-03';

        // Cache the availability
        $this->room->isAvailable($checkIn, $checkOut);
        $this->assertNotNull($this->cacheService->getRoomAvailability($this->room->id, $checkIn, $checkOut));

        // Change room status
        $this->room->update(['status' => 'maintenance']);

        // Cache should be invalidated for this room
        $this->assertNull($this->cacheService->getRoomAvailability($this->room->id, $checkIn, $checkOut));
    }

    /** @test */
    public function it_can_handle_cache_failures_gracefully()
    {
        // Simulate cache failure by using array driver temporarily
        config(['cache.default' => 'array']);

        $checkIn = '2025-09-01';
        $checkOut = '2025-09-03';

        // Should still return correct availability even without cache
        $isAvailable = $this->room->isAvailable($checkIn, $checkOut);
        $this->assertTrue($isAvailable);
    }

    /** @test */
    public function it_can_warm_up_cache()
    {
        // Create additional rooms for testing
        Room::factory()->count(5)->create([
            'room_type_id' => $this->room->room_type_id,
            'status' => 'available',
        ]);

        // Warm up cache
        $this->cacheService->warmUpCache();

        // Verify some cache entries were created
        $checkIn = now()->addDay()->format('Y-m-d');
        $checkOut = now()->addDays(2)->format('Y-m-d');

        $cachedResult = $this->cacheService->getRoomAvailability($this->room->id, $checkIn, $checkOut);
        $this->assertNotNull($cachedResult);
    }

    /** @test */
    public function it_can_clear_specific_cache_types()
    {
        // Cache different types of data
        $this->cacheService->cacheRoomAvailability($this->room->id, '2025-09-01', '2025-09-03', true);
        $this->cacheService->cacheSearchResults(['test' => 'params'], [$this->room->id]);
        $this->cacheService->cacheRoomStats(['available' => 1]);

        // Clear only availability cache
        Cache::tags(['room_availability'])->flush();

        // Availability cache should be cleared
        $this->assertNull($this->cacheService->getRoomAvailability($this->room->id, '2025-09-01', '2025-09-03'));

        // Other caches should still exist
        $this->assertNotNull($this->cacheService->getSearchResults(['test' => 'params']));
        $this->assertNotNull($this->cacheService->getRoomStats());
    }

    /** @test */
    public function it_handles_concurrent_cache_operations()
    {
        $checkIn = '2025-09-01';
        $checkOut = '2025-09-03';

        // Simulate concurrent requests
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $results[] = $this->room->isAvailable($checkIn, $checkOut);
        }

        // All should return the same result
        $this->assertEquals(1, count(array_unique($results)));
        $this->assertTrue($results[0]);
    }

    /** @test */
    public function room_availability_api_uses_cache()
    {
        $response = $this->postJson("/api/rooms/{$this->room->id}/check-availability", [
            'check_in' => '2025-09-01',
            'check_out' => '2025-09-03',
        ]);

        $response->assertOk();
        $response->assertJson(['available' => true]);

        // Second request should indicate cache was used
        $response2 = $this->postJson("/api/rooms/{$this->room->id}/check-availability", [
            'check_in' => '2025-09-01',
            'check_out' => '2025-09-03',
        ]);

        $response2->assertOk();
        $response2->assertJson(['cached' => true]);
    }

    protected function tearDown(): void
    {
        // Clear all cache to prevent test pollution
        Cache::flush();
        parent::tearDown();
    }
}
