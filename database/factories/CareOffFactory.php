<?php

namespace Database\Factories;

use App\Models\CareOff;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareOff>
 */
class CareOffFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = CareOff::class;

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
