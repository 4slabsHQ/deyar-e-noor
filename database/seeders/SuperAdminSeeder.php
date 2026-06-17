<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'superadmin@travel.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@travel.com',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        $user->assignRole('Super Admin');

        $this->command->info('Super Admin created: superadmin@travel.com / Admin@12345');
    }
}