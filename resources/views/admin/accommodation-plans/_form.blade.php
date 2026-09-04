@php
    use App\Enums\AccommodationPlanSlot;
    use App\Enums\AccommodationPlanType;

    $accommodationPlan = $accommodationPlan ?? null;
    $selectedType = old('type', $accommodationPlan?->type?->value ?? $planType->value);
    $slotValues = [];

    foreach ($accommodationPlan?->slots ?? [] as $slot) {
        $slotValues[$slot->slot->value] = [
            'property_id' => $slot->property_id,
            'property_akad_id' => $slot->property_akad_id,
        ];
    }

    $propertyAkadsMap = collect($propertiesBySlot)
        ->flatten(1)
        ->unique('id')
        ->mapWithKeys(fn ($property) => [$property->id => $property->akads->map(fn ($akad) => ['id' => $akad->id, 'label' => $akad->optionLabel()])->values()])
        ->all();
@endphp

<x-admin.form-grid>
    <x-admin.form-field label="Plan name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $accommodationPlan->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Type" for="type" class="col-lg-4 col-md-6" :required="true">
        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
            @foreach (AccommodationPlanType::cases() as $type)
                <option value="{{ $type->value }}" @selected($selectedType === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-3 col-md-4">
        <x-admin.form-switch :checked="$accommodationPlan->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>

<div class="mt-4">
    <h5 class="mb-3">Accommodation slots</h5>
    @error('slots') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

    @foreach (AccommodationPlanSlot::cases() as $slot)
        @php
            $isShiftingOnly = $slot === AccommodationPlanSlot::ShiftingBuilding;
            $selectedPropertyId = old('slots.'.$slot->value.'.property_id', $slotValues[$slot->value]['property_id'] ?? '');
            $selectedAkadId = old('slots.'.$slot->value.'.property_akad_id', $slotValues[$slot->value]['property_akad_id'] ?? '');
            $properties = $propertiesBySlot[$slot->value] ?? collect();
        @endphp
        <div class="card admin-index-card mb-3 plan-slot-card @if($isShiftingOnly) shifting-only-slot @endif" @if($isShiftingOnly && $selectedType !== AccommodationPlanType::Shifting->value) style="display:none" @endif>
            <div class="card-body">
                <h6 class="mb-3">{{ $slot->label() }}</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Property</label>
                        <select name="slots[{{ $slot->value }}][property_id]" class="form-control js-searchable-select slot-property-select @error('slots.'.$slot->value.'.property_id') is-invalid @enderror" data-slot="{{ $slot->value }}" required>
                            <option value="">Select property</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->id }}" @selected((string) $selectedPropertyId === (string) $property->id)>{{ $property->registrationOptionLabel() }}</option>
                            @endforeach
                        </select>
                        @error('slots.'.$slot->value.'.property_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Akad <span class="text-muted">(optional)</span></label>
                        <select name="slots[{{ $slot->value }}][property_akad_id]" class="form-control js-searchable-select slot-akad-select @error('slots.'.$slot->value.'.property_akad_id') is-invalid @enderror" data-slot="{{ $slot->value }}">
                            <option value="">Any / not set</option>
                            @foreach ($properties as $property)
                                @foreach ($property->akads as $akad)
                                    <option value="{{ $akad->id }}" data-property-id="{{ $property->id }}" @selected((string) $selectedAkadId === (string) $akad->id)>{{ $akad->optionLabel() }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('slots.'.$slot->value.'.property_akad_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var typeSelect = document.getElementById('type');
            var propertyAkads = @json($propertyAkadsMap);

            function toggleShiftingSlot() {
                var show = typeSelect.value === '{{ AccommodationPlanType::Shifting->value }}';
                document.querySelectorAll('.shifting-only-slot').forEach(function (card) {
                    card.style.display = show ? '' : 'none';
                    card.querySelectorAll('select').forEach(function (select) {
                        select.required = show;
                        if (!show) {
                            select.value = '';
                        }
                    });
                });
            }

            function filterAkads(slotKey) {
                var propertySelect = document.querySelector('.slot-property-select[data-slot="' + slotKey + '"]');
                var akadSelect = document.querySelector('.slot-akad-select[data-slot="' + slotKey + '"]');
                if (!propertySelect || !akadSelect) {
                    return;
                }

                var propertyId = propertySelect.value;
                Array.from(akadSelect.options).forEach(function (option, index) {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }
                    var matches = option.dataset.propertyId === propertyId;
                    option.hidden = !matches;
                    if (!matches && option.selected) {
                        akadSelect.value = '';
                    }
                });
            }

            typeSelect?.addEventListener('change', toggleShiftingSlot);

            document.querySelectorAll('.slot-property-select').forEach(function (select) {
                select.addEventListener('change', function () {
                    filterAkads(select.dataset.slot);
                });
                filterAkads(select.dataset.slot);
            });

            toggleShiftingSlot();
        });
    </script>
@endpush
