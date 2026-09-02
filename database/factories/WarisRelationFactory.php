<?php

namespace Database\Factories;

use App\Models\WarisRelation;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WarisRelation>
 */
class WarisRelationFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = WarisRelation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => fake()->unique()->randomElement(['Son', 'Daughter', 'Brother', 'Nephew']),
            'is_active' => true,
        ];
    }
}
