<?php

namespace Database\Factories;

use App\Models\RoomType;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomType>
 */
class RoomTypeFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = RoomType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => fake()->unique()->randomElement(['Sharing', 'Double', 'Triple', 'Quad']),
            'is_active' => true,
        ];
    }
}
