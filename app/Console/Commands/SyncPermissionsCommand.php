<?php

namespace App\Console\Commands;

use App\Services\PermissionSyncService;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'Sync permissions from the catalog without changing roles, users, or master data';

    public function handle(PermissionSyncService $permissionSyncService): int
    {
        $permissionSyncService->sync();

        $this->components->info('Permissions synced successfully.');

        return self::SUCCESS;
    }
}
