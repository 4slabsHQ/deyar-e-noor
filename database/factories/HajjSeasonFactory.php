<?php

namespace Database\Factories;

use App\Enums\HajjSeasonStatus;
use App\Models\HajjSeason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HajjSeason>
 */
class HajjSeasonFactory extends Factory
{
    protected $model = HajjSeason::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'year' => (int) fake()->unique()->numberBetween(2020, 2035),
            'status' => HajjSeasonStatus::Archived,
            'activated_at' => null,
            'activated_by' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => HajjSeasonStatus::Active,
            'activated_at' => now(),
        ]);
    }
}
