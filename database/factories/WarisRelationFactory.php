<?php

namespace Database\Factories;

use App\Models\WarisRelation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WarisRelation>
 */
class WarisRelationFactory extends Factory
{
    protected $model = WarisRelation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Son', 'Daughter', 'Brother', 'Nephew']),
            'is_active' => true,
        ];
    }
}
