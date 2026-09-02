<?php

namespace Database\Factories;

use App\Models\MaktabCategory;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaktabCategory>
 */
class MaktabCategoryFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = MaktabCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => fake()->unique()->words(2, true),
            'zone' => 'Zone '.fake()->numberBetween(1, 5),
            'is_active' => true,
        ];
    }
}
