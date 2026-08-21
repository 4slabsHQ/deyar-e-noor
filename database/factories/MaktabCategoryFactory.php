<?php

namespace Database\Factories;

use App\Models\MaktabCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaktabCategory>
 */
class MaktabCategoryFactory extends Factory
{
    protected $model = MaktabCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'zone' => 'Zone '.fake()->numberBetween(1, 5),
            'is_active' => true,
        ];
    }
}
