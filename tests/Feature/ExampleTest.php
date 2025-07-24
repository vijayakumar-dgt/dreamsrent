<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the home page loads successfully.
     */
    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test that the vehicle list page loads successfully.
     */
    public function test_vehicle_list_page_loads_successfully(): void
    {
        $response = $this->get('/vehicles');
        $response->assertStatus(200);
    }

    /**
     * Test that the contact us page loads successfully.
     */
    public function test_contact_us_page_loads_successfully(): void
    {
        $response = $this->get('/contact-us');
        $response->assertStatus(200);
    }

    /**
     * Test API health check endpoint.
     */
    public function test_api_health_check(): void
    {
        $response = $this->get('/api/health');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'timestamp',
                     'version'
                 ])
                 ->assertJson([
                     'status' => 'ok'
                 ]);
    }
}
