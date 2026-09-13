<?php

namespace App\Services;

use App\Enums\AccommodationPlanSlot;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\PilgrimAccommodationSlot;
use App\Models\PropertyAkad;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PilgrimPackageRegistrationService
{
    /** @return array<string, mixed> */
    public function registrationDetailsForPackage(Package $package): array
    {
        $package->loadMissing([
            'route',
            'accommodationPlan.slots.property.akads',
            'accommodationPlan.slots.akad',
        ]);

        if ($package->route_id === null || $package->accommodation_plan_id === null || $package->accommodationPlan === null) {
            throw new HttpResponseException(response()->json([
                'message' => 'Selected package is missing a route or accommodation plan.',
            ], 422));
        }

        $plan = $package->accommodationPlan;

        return [
            'route_id' => $package->route_id,
            'days' => $package->days,
            'duration' => $package->duration?->value,
            'qurbani_included' => $package->qurbani_included,
            'plan_type' => $plan->type->value,
            'slots' => $plan->slots
                ->sortBy('sequence')
                ->map(fn ($planSlot): array => [
                    'key' => $planSlot->slot->value,
                    'label' => $planSlot->slot->label(),
                    'property_name' => $planSlot->property?->name,
                    'property_label' => $planSlot->property?->registrationOptionLabel(),
                    'default_akad_id' => $planSlot->property_akad_id,
                    'akads' => $planSlot->property?->akads
                        ->map(fn (PropertyAkad $akad): array => [
                            'id' => $akad->id,
                            'label' => $akad->optionLabel(),
                        ])
                        ->values()
                        ->all() ?? [],
                ])
                ->values()
                ->all(),
        ];
    }

    /** @param  array<string, array<string, mixed>>  $slotsInput */
    public function syncAccommodationSlots(Pilgrim $pilgrim, array $slotsInput, ?Package $package = null): void
    {
        $package ??= $pilgrim->package;

        $applicableSlots = $this->applicableSlotKeys($package);

        $pilgrim->accommodationSlots()->delete();

        foreach ($applicableSlots as $slotKey) {
            $row = $slotsInput[$slotKey] ?? null;

            if ($row === null) {
                continue;
            }

            $akadId = filled($row['property_akad_id'] ?? null) ? (int) $row['property_akad_id'] : null;
            $roomNumber = filled($row['room_number'] ?? null) ? (string) $row['room_number'] : null;

            if ($akadId === null && $roomNumber === null) {
                continue;
            }

            $pilgrim->accommodationSlots()->create([
                'slot' => $slotKey,
                'property_akad_id' => $akadId,
                'room_number' => $roomNumber,
            ]);
        }
    }

    /** @return list<string> */
    public function applicableSlotKeys(?Package $package): array
    {
        if ($package?->accommodationPlan === null) {
            return [];
        }

        return array_map(
            fn (AccommodationPlanSlot $slot): string => $slot->value,
            $package->accommodationPlan->type->slots(),
        );
    }

    /** @param  array<string, array<string, mixed>>  $slotsInput */
    public function validateAccommodationSlots(array $slotsInput, ?Package $package): void
    {
        if ($package === null) {
            return;
        }

        $package->loadMissing('accommodationPlan.slots.property');

        if ($package->accommodationPlan === null) {
            throw ValidationException::withMessages([
                'package_id' => 'Selected package is missing an accommodation plan.',
            ]);
        }

        $planSlots = $package->accommodationPlan->slots->keyBy(fn ($slot) => $slot->slot->value);
        $applicable = $this->applicableSlotKeys($package);

        foreach ($slotsInput as $slotKey => $row) {
            if (! in_array($slotKey, $applicable, true)) {
                throw ValidationException::withMessages([
                    'accommodation_slots.'.$slotKey => 'This accommodation slot is not valid for the selected package.',
                ]);
            }

            $akadId = $row['property_akad_id'] ?? null;

            if ($akadId === null || $akadId === '') {
                continue;
            }

            $planSlot = $planSlots->get($slotKey);
            $akad = PropertyAkad::query()->find((int) $akadId);

            if ($akad === null) {
                throw ValidationException::withMessages([
                    'accommodation_slots.'.$slotKey.'.property_akad_id' => 'Selected akad was not found.',
                ]);
            }

            if ((int) $akad->property_id !== (int) $planSlot?->property_id) {
                throw ValidationException::withMessages([
                    'accommodation_slots.'.$slotKey.'.property_akad_id' => 'Selected akad does not belong to the package property for this slot.',
                ]);
            }
        }
    }

    /** @return Collection<int, PilgrimAccommodationSlot> */
    public function accommodationSlotsKeyed(Pilgrim $pilgrim): Collection
    {
        return $pilgrim->accommodationSlots->keyBy(fn (PilgrimAccommodationSlot $slot) => $slot->slot->value);
    }
}
