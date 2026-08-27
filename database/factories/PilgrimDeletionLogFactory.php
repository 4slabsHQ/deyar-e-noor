<?php

namespace Database\Factories;

use App\Models\Pilgrim;
use App\Models\PilgrimDeletionLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PilgrimDeletionLog>
 */
class PilgrimDeletionLogFactory extends Factory
{
    protected $model = PilgrimDeletionLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pilgrim_id' => Pilgrim::factory(),
            'deleted_by' => User::factory(),
            'deleted_at' => now(),
            'hajj_year' => (int) date('Y'),
            'full_name' => fake()->name(),
            'passport_no' => strtoupper(fake()->bothify('??#######')),
            'family_code' => 'DYN-01-S',
            'company_name' => fake()->company(),
            'package_label' => 'PKG-001 Economy',
            'pod_city_name' => fake()->city(),
            'gender' => 'Male',
            'mobile' => fake()->numerify('03#########'),
            'entry_date' => now()->toDateString(),
        ];
    }
}
