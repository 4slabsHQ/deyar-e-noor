<?php

namespace Database\Factories;

use App\Models\Airport;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Airport>
 */
class AirportFactory extends Factory
{
    protected $model = Airport::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' International Airport',
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'city_id' => City::factory(),
            'is_active' => true,
        ];
    }
}
