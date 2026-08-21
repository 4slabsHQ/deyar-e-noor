<?php

namespace Database\Factories;

use App\Enums\PackageDuration;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    protected $model = Package::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => 'PKG-'.fake()->unique()->numerify('###'),
            'name' => fake()->words(2, true),
            'price' => fake()->numberBetween(500000, 1500000),
            'days' => fake()->randomElement([14, 21, 28]),
            'qurbani_included' => fake()->boolean(),
            'duration' => fake()->randomElement(PackageDuration::cases()),
            'is_active' => true,
        ];
    }
}
