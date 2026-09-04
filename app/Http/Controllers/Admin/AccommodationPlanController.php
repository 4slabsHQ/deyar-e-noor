<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccommodationPlanSlot;
use App\Enums\AccommodationPlanType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccommodationPlanRequest;
use App\Http\Requests\UpdateAccommodationPlanRequest;
use App\Models\AccommodationPlan;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class AccommodationPlanController extends Controller
{
    public function index()
    {
        $accommodationPlans = AccommodationPlan::query()
            ->forActiveYear()
            ->with(['slots.property', 'slots.akad'])
            ->orderBy('name')
            ->get();

        return view('admin.accommodation-plans.index', compact('accommodationPlans'));
    }

    public function create()
    {
        return view('admin.accommodation-plans.create', $this->formData());
    }

    public function store(StoreAccommodationPlanRequest $request)
    {
        DB::transaction(function () use ($request): void {
            $plan = AccommodationPlan::create($this->withActiveHajjYear($request->safe()->except('slots')));
            $this->syncSlots($plan, $request->input('slots', []));
        });

        return redirect()->route('admin.accommodation-plans.index')->with('success', 'Accommodation plan created successfully.');
    }

    public function edit(AccommodationPlan $accommodationPlan)
    {
        $accommodationPlan->load(['slots.property', 'slots.akad']);

        return view('admin.accommodation-plans.edit', array_merge(
            ['accommodationPlan' => $accommodationPlan],
            $this->formData($accommodationPlan->type),
        ));
    }

    public function update(UpdateAccommodationPlanRequest $request, AccommodationPlan $accommodationPlan)
    {
        DB::transaction(function () use ($request, $accommodationPlan): void {
            $accommodationPlan->update($request->safe()->except('slots'));
            $this->syncSlots($accommodationPlan, $request->input('slots', []));
        });

        return redirect()->route('admin.accommodation-plans.index')->with('success', 'Accommodation plan updated successfully.');
    }

    public function destroy(AccommodationPlan $accommodationPlan)
    {
        return $this->deleteOrBack(
            $accommodationPlan,
            'admin.accommodation-plans.index',
            'Accommodation plan deleted successfully.',
        );
    }

    /** @return array<string, mixed> */
    private function formData(?AccommodationPlanType $type = null): array
    {
        $type ??= AccommodationPlanType::tryFrom((string) old('type')) ?? AccommodationPlanType::Still;

        $propertiesBySlot = [];

        foreach (AccommodationPlanSlot::cases() as $slot) {
            $propertiesBySlot[$slot->value] = Property::query()
                ->forActiveYear()
                ->where('city', $slot->propertyCity())
                ->whereIn('type', $slot->propertyTypes())
                ->where('is_active', true)
                ->orderBy('name')
                ->with('akads')
                ->get();
        }

        return [
            'planType' => $type,
            'propertiesBySlot' => $propertiesBySlot,
        ];
    }

    /** @param  array<string, array<string, mixed>>  $slots */
    private function syncSlots(AccommodationPlan $plan, array $slots): void
    {
        $plan->slots()->delete();

        foreach ($plan->type->slots() as $index => $slot) {
            $row = $slots[$slot->value] ?? null;

            if ($row === null) {
                continue;
            }

            $plan->slots()->create([
                'slot' => $slot->value,
                'property_id' => (int) $row['property_id'],
                'property_akad_id' => filled($row['property_akad_id'] ?? null) ? (int) $row['property_akad_id'] : null,
                'sequence' => $index + 1,
            ]);
        }
    }
}
