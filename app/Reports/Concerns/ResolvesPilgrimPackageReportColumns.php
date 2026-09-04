<?php

namespace App\Reports\Concerns;

use App\Enums\AccommodationPlanSlot;
use App\Models\Pilgrim;

trait ResolvesPilgrimPackageReportColumns
{
    /** @return array<string, array{label: string, group: string}> */
    protected function pilgrimPackageColumnCatalog(string $group): array
    {
        return [
            'days' => ['label' => 'Days', 'group' => $group],
            'duration' => ['label' => 'Duration', 'group' => $group],
            'qurbani_included' => ['label' => 'Qurbani', 'group' => $group],
            'route' => ['label' => 'Route', 'group' => $group],
            'route_path' => ['label' => 'Route Path', 'group' => $group],
            'accommodation_plan' => ['label' => 'Accommodation Plan', 'group' => $group],
            'accommodation_plan_type' => ['label' => 'Plan Type', 'group' => $group],
            'makkah_hotel' => ['label' => 'Makkah Hotel', 'group' => $group],
            'madinah_hotel' => ['label' => 'Madinah Hotel', 'group' => $group],
            'shifting_building' => ['label' => 'Shifting Building', 'group' => $group],
        ];
    }

    /** @param  list<string>  $columns
     * @return list<string>
     */
    protected function pilgrimPackageRelationsForColumns(array $columns): array
    {
        $packageColumnKeys = array_keys($this->pilgrimPackageColumnCatalog(''));
        $packageColumnKeys[] = 'package';

        if (array_intersect($columns, $packageColumnKeys) === []) {
            return [];
        }

        $relations = [
            'package:id,name,number,price,days,duration,qurbani_included,accommodation_plan_id,route_id',
        ];

        if (array_intersect($columns, ['route', 'route_path']) !== []) {
            $relations[] = 'package.route.steps.airport';
            $relations[] = 'package.route.steps.city';
        }

        if (array_intersect($columns, [
            'accommodation_plan',
            'accommodation_plan_type',
            'makkah_hotel',
            'madinah_hotel',
            'shifting_building',
        ]) !== []) {
            $relations[] = 'package.accommodationPlan';
            $relations[] = 'package.accommodationPlan.slots.property';
            $relations[] = 'package.accommodationPlan.slots.akad';
        }

        return $relations;
    }

    protected function resolvePilgrimPackageColumn(Pilgrim $pilgrim, string $column): string|int|null
    {
        return match ($column) {
            'days' => $pilgrim->days !== null
                ? (string) $pilgrim->days
                : ($pilgrim->package?->days !== null ? (string) $pilgrim->package->days : null),
            'duration' => ($pilgrim->duration ?? $pilgrim->package?->duration)?->label(),
            'qurbani_included' => $pilgrim->qurbani_included ? 'Yes' : 'No',
            'route' => $pilgrim->package?->route?->name,
            'route_path' => $pilgrim->package?->route?->summary() ?: null,
            'accommodation_plan' => $pilgrim->package?->accommodationPlan?->name,
            'accommodation_plan_type' => $pilgrim->package?->accommodationPlan?->type->label(),
            'makkah_hotel' => $this->accommodationSlotLabel($pilgrim, AccommodationPlanSlot::MakkahHotel),
            'madinah_hotel' => $this->accommodationSlotLabel($pilgrim, AccommodationPlanSlot::MadinahHotel),
            'shifting_building' => $this->accommodationSlotLabel($pilgrim, AccommodationPlanSlot::ShiftingBuilding),
            default => null,
        };
    }

    private function accommodationSlotLabel(Pilgrim $pilgrim, AccommodationPlanSlot $slotType): ?string
    {
        $plan = $pilgrim->package?->accommodationPlan;

        if ($plan === null) {
            return null;
        }

        $slot = $plan->slots->firstWhere('slot', $slotType);

        return $slot?->displayLabel();
    }
}
