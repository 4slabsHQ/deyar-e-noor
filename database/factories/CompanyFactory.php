<?php

namespace Database\Factories;

use App\Models\Company;
use Database\Factories\Concerns\UsesActiveHajjYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    use UsesActiveHajjYear;

    protected $model = Company::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'hajj_year' => $this->activeHajjYear(),
            'name' => fake()->company(),
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'legal_name' => fake()->company().' (Pvt) Ltd.',
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'currency' => 'PKR',
            'timezone' => 'Asia/Karachi',
            'is_active' => true,
        ];
    }
}
