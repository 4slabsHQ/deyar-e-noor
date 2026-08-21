<?php

namespace Database\Factories;

use App\Models\FormOwner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormOwner>
 */
class FormOwnerFactory extends Factory
{
    protected $model = FormOwner::class;

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
