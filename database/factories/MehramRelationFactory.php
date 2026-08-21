<?php

namespace Database\Factories;

use App\Models\MehramRelation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MehramRelation>
 */
class MehramRelationFactory extends Factory
{
    protected $model = MehramRelation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Husband', 'Father', 'Brother', 'Son']),
            'is_active' => true,
        ];
    }
}
