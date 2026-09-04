<?php

namespace Database\Factories;

use App\Enums\AccommodationPlanType;
use App\Models\AccommodationPlan;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccommodationPlan>
 */
class AccommodationPlanFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = AccommodationPlan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => 'Plan '.fake()->unique()->word(),
            'type' => AccommodationPlanType::Still,
            'is_active' => true,
        ];
    }
}
