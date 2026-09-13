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
            'makkah_hotel_akad' => ['label' => 'Makkah Hotel Akad', 'group' => $group],
            'makkah_hotel_room' => ['label' => 'Makkah Hotel Room', 'group' => $group],
            'madinah_hotel' => ['label' => 'Madinah Hotel', 'group' => $group],
            'madinah_hotel_akad' => ['label' => 'Madinah Hotel Akad', 'group' => $group],
            'madinah_hotel_room' => ['label' => 'Madinah Hotel Room', 'group' => $group],
            'shifting_building' => ['label' => 'Shifting Building', 'group' => $group],
            'shifting_building_akad' => ['label' => 'Shifting Building Akad', 'group' => $group],
            'shifting_building_room' => ['label' => 'Shifting Building Room', 'group' => $group],
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
            'route.steps.airport',
            'route.steps.city',
            'accommodationSlots.akad',
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
            'makkah_hotel_akad',
            'madinah_hotel_akad',
            'shifting_building_akad',
            'makkah_hotel_room',
            'madinah_hotel_room',
            'shifting_building_room',
        ]) !== []) {
            $relations[] = 'package.accommodationPlan';
            $relations[] = 'package.accommodationPlan.slots.property';
            $relations[] = 'package.accommodationPlan.slots.akad';
        }

        return array_values(array_unique($relations));
    }

    protected function resolvePilgrimPackageColumn(Pilgrim $pilgrim, string $column): string|int|null
    {
        return match ($column) {
            'days' => $pilgrim->days !== null
                ? (string) $pilgrim->days
                : ($pilgrim->package?->days !== null ? (string) $pilgrim->package->days : null),
            'duration' => ($pilgrim->duration ?? $pilgrim->package?->duration)?->label(),
            'qurbani_included' => $pilgrim->qurbani_included ? 'Yes' : 'No',
            'route' => $pilgrim->resolvedRoute()?->name,
            'route_path' => $pilgrim->resolvedRoute()?->summary() ?: null,
            'accommodation_plan' => $pilgrim->package?->accommodationPlan?->name,
            'accommodation_plan_type' => $pilgrim->package?->accommodationPlan?->type->label(),
            'makkah_hotel' => $this->accommodationSlotLabel($pilgrim, AccommodationPlanSlot::MakkahHotel),
            'madinah_hotel' => $this->accommodationSlotLabel($pilgrim, AccommodationPlanSlot::MadinahHotel),
            'shifting_building' => $this->accommodationSlotLabel($pilgrim, AccommodationPlanSlot::ShiftingBuilding),
            'makkah_hotel_akad' => $this->pilgrimSlotAkadLabel($pilgrim, AccommodationPlanSlot::MakkahHotel),
            'madinah_hotel_akad' => $this->pilgrimSlotAkadLabel($pilgrim, AccommodationPlanSlot::MadinahHotel),
            'shifting_building_akad' => $this->pilgrimSlotAkadLabel($pilgrim, AccommodationPlanSlot::ShiftingBuilding),
            'makkah_hotel_room' => $this->pilgrimSlotRoomNumber($pilgrim, AccommodationPlanSlot::MakkahHotel),
            'madinah_hotel_room' => $this->pilgrimSlotRoomNumber($pilgrim, AccommodationPlanSlot::MadinahHotel),
            'shifting_building_room' => $this->pilgrimSlotRoomNumber($pilgrim, AccommodationPlanSlot::ShiftingBuilding),
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

        return $slot?->property?->registrationOptionLabel();
    }

    private function pilgrimSlotAkadLabel(Pilgrim $pilgrim, AccommodationPlanSlot $slotType): ?string
    {
        return $pilgrim->accommodationSlotAssignment($slotType)?->akad?->optionLabel();
    }

    private function pilgrimSlotRoomNumber(Pilgrim $pilgrim, AccommodationPlanSlot $slotType): ?string
    {
        $roomNumber = $pilgrim->accommodationSlotAssignment($slotType)?->room_number;

        return filled($roomNumber) ? $roomNumber : null;
    }
}
