<?php

namespace Database\Factories;

use App\Models\FormOwner;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormOwner>
 */
class FormOwnerFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = FormOwner::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => fake()->unique()->company(),
            'is_active' => true,
        ];
    }
}
