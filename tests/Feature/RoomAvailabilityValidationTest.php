<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomAvailabilityValidationTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $room;
    private $roomType;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        
        $this->roomType = RoomType::create([
            'name' => 'Standard Room',
            'base_price' => 100.00,
            'max_occupancy' => 2,
            'size' => 25.0,
            'amenities' => ['wifi', 'ac'],
        ]);
        
        $this->room = Room::create([
            'room_number' => '101',
            'room_type_id' => $this->roomType->id,
            'floor_number' => 1,
            'size' => 25.0,
            'status' => 'available',
        ]);
    }

    /** @test */
    public function it_allows_booking_for_available_dates()
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($this->user)->post(route('user.bookings.store'), [
            'room_id' => $this->room->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 1,
            'special_requests' => 'Test booking',
            'payment_method' => 'credit_card',
            'terms' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'room_id' => $this->room->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'status' => 'confirmed',
        ]);
    }

    /** @test */
    public function it_prevents_booking_for_conflicting_dates()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);

        // Create existing booking
        Booking::create([
            'booking_reference' => 'BK202500001',
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'check_in_date' => $checkIn->copy()->addDay(), // Day 2
            'check_out_date' => $checkIn->copy()->addDays(2), // Day 3
            'guests_count' => 1,
            'total_amount' => 200.00,
            'status' => 'confirmed',
            'booking_source' => 'online',
        ]);

        // Try to book overlapping dates
        $response = $this->actingAs($this->user)->post(route('user.bookings.store'), [
            'room_id' => $this->room->id,
            'check_in' => $checkIn->format('Y-m-d'), // Day 1
            'check_out' => $checkOut->format('Y-m-d'), // Day 4
            'guests' => 1,
            'special_requests' => 'Conflicting booking',
            'payment_method' => 'credit_card',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('check_in');
    }

    /** @test */
    public function it_prevents_booking_when_room_is_not_available()
    {
        $this->room->update(['status' => 'closed']);

        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($this->user)->post(route('user.bookings.store'), [
            'room_id' => $this->room->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 1,
            'payment_method' => 'credit_card',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('room_id');
    }

    /** @test */
    public function it_prevents_booking_with_too_many_guests()
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($this->user)->post(route('user.bookings.store'), [
            'room_id' => $this->room->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 5, // Exceeds max_occupancy of 2
            'payment_method' => 'credit_card',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('guests');
    }

    /** @test */
    public function it_returns_availability_status_via_api()
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($this->user)->postJson("/api/rooms/{$this->room->id}/check-availability", [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);

        $response->assertJson([
            'available' => true,
            'room_id' => $this->room->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'room_status' => 'available',
        ]);
    }

    /** @test */
    public function api_returns_conflicts_for_unavailable_dates()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);

        // Create conflicting booking
        Booking::create([
            'booking_reference' => 'BK202500002',
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'check_in_date' => $checkIn->copy()->addDay(),
            'check_out_date' => $checkIn->copy()->addDays(2),
            'guests_count' => 1,
            'total_amount' => 200.00,
            'status' => 'confirmed',
            'booking_source' => 'online',
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/rooms/{$this->room->id}/check-availability", [
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
        ]);

        $response->assertJson([
            'available' => false,
            'room_id' => $this->room->id,
        ]);

        $response->assertJsonStructure([
            'conflicts' => [
                '*' => ['check_in', 'check_out', 'status']
            ]
        ]);
    }
}
