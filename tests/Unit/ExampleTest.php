<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Booking\Models\Booking;
use Tests\TestCase;

class BookingModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test booking status constants are properly defined.
     */
    public function test_booking_status_constants_are_defined(): void
    {
        $this->assertEquals(1, Booking::$inprogress);
        $this->assertEquals(2, Booking::$confirmed);
        $this->assertEquals(3, Booking::$rejected);
        $this->assertEquals(4, Booking::$booked);
        $this->assertEquals(5, Booking::$completed);
        $this->assertEquals(6, Booking::$cancelled);
    }

    /**
     * Test booking status label method.
     */
    public function test_booking_status_label_method(): void
    {
        $this->assertNotEmpty(Booking::getStatusLabel(Booking::$inprogress));
        $this->assertNotEmpty(Booking::getStatusLabel(Booking::$confirmed));
        $this->assertNotEmpty(Booking::getStatusLabel(Booking::$completed));
        $this->assertEquals('Unknown', Booking::getStatusLabel(999));
    }

    /**
     * Test booking encrypted ID generation.
     */
    public function test_booking_encrypted_id_generation(): void
    {
        $booking = Booking::factory()->create();
        
        $this->assertNotEmpty($booking->encrypted_id);
        $this->assertIsString($booking->encrypted_id);
    }

    /**
     * Test booking relationships are properly defined.
     */
    public function test_booking_has_proper_relationships(): void
    {
        $booking = new Booking();
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $booking->vehicle()
        );
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $booking->drivingType()
        );
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasOne::class,
            $booking->bookingDetail()
        );
    }

    /**
     * Test user model has booking relationship.
     */
    public function test_user_has_bookings_relationship(): void
    {
        $user = new User();
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $user->bookings()
        );
    }

    /**
     * Test helper functions exist and work properly.
     */
    public function test_helper_functions_work(): void
    {
        // Test formatDateTime helper
        $this->assertIsString(formatDateTime(now()));
        $this->assertIsString(formatDateTime(now(), false));
        $this->assertIsString(formatDateTime(now(), true, true));
        
        // Test clearCache helper
        $this->assertTrue(clearCache());
    }
}
