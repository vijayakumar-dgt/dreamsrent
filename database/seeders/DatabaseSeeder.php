<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\CarInfo\Models\Checklist;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->generateChecklist();
        //  User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }

    public function generateChecklist()
    {
        $checklistArr = [
            [
                'name' => 'Body Condition',
                'description' => 'Look for dents, scratches, rust, or paint damage.'
            ],
            [
                'name' => 'Glass & Mirrors',
                'description' => 'Ensure no cracks, chips, or broken glass.'
            ],
            [
                'name' => 'Lights',
                'description' => 'Inspect the headlights, taillights, and interior lighting.'
            ],
            [
                'name' => 'Tires & Wheels',
                'description' => 'Check the tire pressure, alignment, and condition of the wheels.'
            ],
            [
                'name' => 'Engine Oil, Coolant, & Brake Fluids',
                'description' => 'Inspect the engine oil, coolant, and brake fluids for leaks.'
            ],
            [
                'name' => 'Battery',
                'description' => 'Ensure the battery is fully charged and in good condition.'
            ],
            [
                'name' => 'Seats & Seatbelts',
                'description' => 'Check the condition of the seats, seatbelts, and headrests.'
            ],
            [
                'name' => 'AC & Heater',
                'description' => 'Inspect the AC system and heater for proper operation.'
            ],
            [
                'name' => 'Brakes & Suspension',
                'description' => 'Check the condition of the brakes and suspension system.'
            ],
            [
                'name' => 'Exhaust System',
                'description' => 'Inspect the exhaust system for proper operation.'
            ]
        ];

        foreach ($checklistArr as $checklist) {
            Checklist::create($checklist);
        }
    }
}
