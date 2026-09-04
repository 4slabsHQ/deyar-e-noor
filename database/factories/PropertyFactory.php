<?php

namespace Database\Factories;

use App\Enums\PropertyCity;
use App\Enums\PropertyType;
use App\Models\Property;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = Property::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => fake()->company().' Hotel',
            'city' => PropertyCity::Makkah,
            'type' => PropertyType::Hotel,
            'is_active' => true,
        ];
    }
}
