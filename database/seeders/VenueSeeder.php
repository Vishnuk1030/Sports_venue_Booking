<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            $startHour = rand(5, 7); //  between 5 AM - 7 AM
            $endHour = rand(20, 23); // between 8 PM - 11 PM

            $startTime = sprintf('%02d:00 AM', $startHour <= 12 ? $startHour : $startHour - 12);
            $endTime = sprintf('%02d:00 PM', $endHour - 12);

            Venue::create([
                'venue_name' => $faker->unique()->company . ' Sports Arena',
                'working_hours' => "$startTime - $endTime"
            ]);
        }
    }
}
