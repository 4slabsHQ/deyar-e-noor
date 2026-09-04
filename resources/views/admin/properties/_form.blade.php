@php
    use App\Enums\PropertyCity;
    use App\Enums\PropertyType;

    $property = $property ?? null;
    $akads = old('akads', $property?->akads?->map(fn ($akad) => [
        'id' => $akad->id,
        'akad_number' => $akad->akad_number,
        'label' => $akad->label,
        'notes' => $akad->notes,
    ])->all() ?? [['akad_number' => '', 'label' => '', 'notes' => '']]);
@endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $property->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="City" for="city" class="col-lg-4 col-md-6" :required="true">
        <select name="city" id="city" class="form-control @error('city') is-invalid @enderror" required>
            <option value="" disabled @selected(! old('city', $property?->city?->value))>Select</option>
            @foreach (PropertyCity::cases() as $city)
                <option value="{{ $city->value }}" @selected(old('city', $property?->city?->value) === $city->value)>{{ $city->label() }}</option>
            @endforeach
        </select>
        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Type" for="type" class="col-lg-4 col-md-6" :required="true">
        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
            <option value="" disabled @selected(! old('type', $property?->type?->value))>Select</option>
            @foreach (PropertyType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $property?->type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-3 col-md-4">
        <x-admin.form-switch :checked="$property->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>

<div class="mt-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Akad numbers</h5>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="add-akad-row">Add akad</button>
    </div>
    @error('akads') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
    <div class="table-responsive">
        <table class="table table-sm" id="akad-rows">
            <thead>
                <tr>
                    <th>Akad number</th>
                    <th>Label</th>
                    <th>Notes</th>
                    <th style="width: 4rem;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($akads as $index => $akad)
                    <tr>
                        <td>
                            @if (! empty($akad['id']))
                                <input type="hidden" name="akads[{{ $index }}][id]" value="{{ $akad['id'] }}">
                            @endif
                            <input type="text" name="akads[{{ $index }}][akad_number]" value="{{ $akad['akad_number'] ?? '' }}"
                                   class="form-control form-control-sm @error('akads.'.$index.'.akad_number') is-invalid @enderror">
                            @error('akads.'.$index.'.akad_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </td>
                        <td><input type="text" name="akads[{{ $index }}][label]" value="{{ $akad['label'] ?? '' }}" class="form-control form-control-sm"></td>
                        <td><input type="text" name="akads[{{ $index }}][notes]" value="{{ $akad['notes'] ?? '' }}" class="form-control form-control-sm"></td>
                        <td><button type="button" class="btn btn-outline-danger btn-sm remove-akad-row">&times;</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tableBody = document.querySelector('#akad-rows tbody');
            var addButton = document.getElementById('add-akad-row');
            var nextIndex = {{ count($akads) }};

            addButton?.addEventListener('click', function () {
                var row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="text" name="akads[${nextIndex}][akad_number]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="akads[${nextIndex}][label]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="akads[${nextIndex}][notes]" class="form-control form-control-sm"></td>
                    <td><button type="button" class="btn btn-outline-danger btn-sm remove-akad-row">&times;</button></td>
                `;
                tableBody.appendChild(row);
                nextIndex++;
            });

            tableBody?.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-akad-row')) {
                    var rows = tableBody.querySelectorAll('tr');
                    if (rows.length > 1) {
                        event.target.closest('tr')?.remove();
                    }
                }
            });
        });
    </script>
@endpush
