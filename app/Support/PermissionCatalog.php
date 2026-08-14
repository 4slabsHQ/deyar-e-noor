<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Spatie\Permission\Models\Permission;

class PermissionCatalog
{
    /**
     * @return array<string, array{label: string, permissions: list<string>}>
     */
    public static function groups(): array
    {
        return [
            'access' => [
                'label' => 'Access Control',
                'permissions' => [
                    'users.view', 'users.create', 'users.update', 'users.delete',
                    'roles.view', 'roles.create', 'roles.update', 'roles.delete',
                ],
            ],
            'companies' => [
                'label' => 'Companies',
                'permissions' => [
                    'companies.view', 'companies.create', 'companies.edit', 'companies.destroy',
                ],
            ],
            'hajj_masters' => [
                'label' => 'Hajj Masters',
                'permissions' => [
                    'form-owners.view', 'form-owners.create', 'form-owners.update', 'form-owners.delete',
                    'maktab-categories.view', 'maktab-categories.create', 'maktab-categories.update', 'maktab-categories.delete',
                    'care-offs.view', 'care-offs.create', 'care-offs.update', 'care-offs.delete',
                    'packages.view', 'packages.create', 'packages.update', 'packages.delete',
                    'room-types.view', 'room-types.create', 'room-types.update', 'room-types.delete',
                    'mehram-relations.view', 'mehram-relations.create', 'mehram-relations.update', 'mehram-relations.delete',
                    'waris-relations.view', 'waris-relations.create', 'waris-relations.update', 'waris-relations.delete',
                ],
            ],
            'geographic' => [
                'label' => 'Geographic & Travel',
                'permissions' => [
                    'countries.view', 'countries.create', 'countries.update', 'countries.delete',
                    'cities.view', 'cities.create', 'cities.update', 'cities.delete',
                    'airlines.view', 'airlines.create', 'airlines.update', 'airlines.delete',
                    'airports.view', 'airports.create', 'airports.update', 'airports.delete',
                ],
            ],
            'hajj_registration' => [
                'label' => 'Hajj Registration',
                'permissions' => [
                    'pilgrims.view', 'pilgrims.create', 'pilgrims.update', 'pilgrims.delete',
                ],
            ],
            'flights' => [
                'label' => 'Flights',
                'permissions' => [
                    'flights.view', 'flights.create', 'flights.update', 'flights.delete',
                ],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function allPermissionNames(): array
    {
        $names = [];

        foreach (self::groups() as $group) {
            array_push($names, ...$group['permissions']);
        }

        return array_values(array_unique($names));
    }

    /**
     * @return array<string, list<string>>
     */
    public static function groupedPermissions(): array
    {
        $grouped = [];

        foreach (self::groups() as $group) {
            $grouped[$group['label']] = $group['permissions'];
        }

        return $grouped;
    }

    /**
     * @return EloquentCollection<int, Permission>
     */
    public static function permissions(): EloquentCollection
    {
        return Permission::query()
            ->whereIn('name', self::allPermissionNames())
            ->orderBy('name')
            ->get();
    }
}
