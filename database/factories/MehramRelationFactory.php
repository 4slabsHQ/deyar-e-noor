<?php

namespace Database\Factories;

use App\Models\MehramRelation;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MehramRelation>
 */
class MehramRelationFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = MehramRelation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => fake()->unique()->randomElement(['Husband', 'Father', 'Brother', 'Son']),
            'is_active' => true,
        ];
    }
}
