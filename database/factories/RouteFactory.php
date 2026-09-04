<?php

namespace Database\Factories;

use App\Models\Route;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = Route::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => 'Route '.fake()->unique()->numerify('##'),
            'is_active' => true,
        ];
    }
}
