<?php

namespace Database\Seeders;

use App\Services\PermissionSyncService;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionSyncService::class)->sync();

        $this->command?->info('Roles and permissions seeded successfully.');
    }
}
