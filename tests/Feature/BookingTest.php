<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleInfo;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $customer;
    protected User $admin;
    protected VehicleInfo $vehicle;
    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->customer = User::factory()->create([
            'user_type' => 3, // Customer
            'status' => 1
        ]);

        $this->admin = User::factory()->create([
            'user_type' => 1, // Admin
            'status' => 1
        ]);

        // Create test location
        $this->location = Location::factory()->create([
            'status' => 1,
            'language_id' => 1
        ]);

        // Create test vehicle
        $this->vehicle = VehicleInfo::factory()->create([
            'status' => 1,
            'language_id' => 1
        ]);
    }

    /**
     * Test that authenticated users can access booking page.
     */
    public function test_authenticated_user_can_access_booking_page(): void
    {
        $response = $this->actingAs($this->customer)
                         ->get('/user/bookings');

        $response->assertStatus(200);
    }

    /**
     * Test that unauthenticated users cannot access booking page.
     */
    public function test_unauthenticated_user_cannot_access_booking_page(): void
    {
        $response = $this->get('/user/bookings');

        $response->assertRedirect('/login');
    }

    /**
     * Test booking creation validation.
     */
    public function test_booking_creation_requires_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
                         ->postJson('/api/admin/bookings', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'start_date',
                     'end_date',
                     'vehicle_id',
                     'customer_id'
                 ]);
    }

    /**
     * Test successful booking creation.
     */
    public function test_admin_can_create_booking_with_valid_data(): void
    {
        $bookingData = [
            'start_date' => now()->addDay()->format('d-m-Y'),
            'start_time' => '10:00',
            'end_date' => now()->addDays(3)->format('d-m-Y'),
            'end_time' => '18:00',
            'pickup_location' => $this->location->id,
            'return_location' => $this->location->id,
            'vehicle_id' => $this->vehicle->id,
            'customer_id' => $this->customer->id,
            'vehicle_price' => 100.00,
            'extra_service' => [],
            'insurance' => [],
            'rental_type' => 'daily'
        ];

        $response = $this->actingAs($this->admin)
                         ->postJson('/api/admin/bookings', $bookingData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'vehicle_id' => $this->vehicle->id,
            'customer_id' => $this->customer->id
        ]);
    }

    /**
     * Test booking date validation.
     */
    public function test_booking_start_date_must_be_future(): void
    {
        $bookingData = [
            'start_date' => now()->subDay()->format('d-m-Y'),
            'end_date' => now()->addDay()->format('d-m-Y'),
            'vehicle_id' => $this->vehicle->id,
            'customer_id' => $this->customer->id,
        ];

        $response = $this->actingAs($this->admin)
                         ->postJson('/api/admin/bookings', $bookingData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['start_date']);
    }

    /**
     * Test that end date must be after start date.
     */
    public function test_booking_end_date_must_be_after_start_date(): void
    {
        $bookingData = [
            'start_date' => now()->addDays(2)->format('d-m-Y'),
            'end_date' => now()->addDay()->format('d-m-Y'),
            'vehicle_id' => $this->vehicle->id,
            'customer_id' => $this->customer->id,
        ];

        $response = $this->actingAs($this->admin)
                         ->postJson('/api/admin/bookings', $bookingData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['end_date']);
    }

    /**
     * Test customer can view their own bookings.
     */
    public function test_customer_can_view_own_bookings(): void
    {
        // Create a booking for the customer
        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id
        ]);

        $response = $this->actingAs($this->customer)
                         ->getJson('/api/user/bookings');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'vehicle_id',
                             'customer_id',
                             'booking_status'
                         ]
                     ]
                 ]);
    }

    /**
     * Test customer cannot view other customers' bookings.
     */
    public function test_customer_cannot_view_other_customers_bookings(): void
    {
        $otherCustomer = User::factory()->create(['user_type' => 3]);
        
        // Create booking for other customer
        $booking = Booking::factory()->create([
            'customer_id' => $otherCustomer->id,
            'vehicle_id' => $this->vehicle->id
        ]);

        $response = $this->actingAs($this->customer)
                         ->getJson('/api/bookings/' . $booking->id);

        $response->assertStatus(403);
    }

    /**
     * Test booking status updates.
     */
    public function test_admin_can_update_booking_status(): void
    {
        $booking = Booking::factory()->create([
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'booking_status' => Booking::$inprogress
        ]);

        $response = $this->actingAs($this->admin)
                         ->putJson('/api/admin/bookings/' . $booking->id, [
                             'booking_status' => Booking::$confirmed
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => Booking::$confirmed
        ]);
    }
}