<?php

namespace Database\Factories;

use App\Models\CareOff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareOff>
 */
class CareOffFactory extends Factory
{
    protected $model = CareOff::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'is_active' => true,
        ];
    }
}
