<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->country().' '.fake()->unique()->numerify('####'),
            'iso2' => strtoupper(fake()->unique()->bothify('??##')),
            'iso3' => strtoupper(fake()->unique()->bothify('???##')),
            'phone_code' => fake()->numberBetween(1, 999),
            'is_active' => true,
        ];
    }
}
