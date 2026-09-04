@php
    use App\Enums\RoutePointType;

    $route = $route ?? null;
    $steps = old('steps', $route?->steps?->map(fn ($step) => [
        'point_type' => $step->point_type->value,
        'airport_id' => $step->airport_id,
        'city_id' => $step->city_id,
    ])->all() ?? [
        ['point_type' => RoutePointType::Airport->value, 'airport_id' => '', 'city_id' => ''],
        ['point_type' => RoutePointType::City->value, 'airport_id' => '', 'city_id' => ''],
    ]);
@endphp

<x-admin.form-grid>
    <x-admin.form-field label="Route name" for="name" class="col-lg-6 col-md-8" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $route->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-3 col-md-4">
        <x-admin.form-switch :checked="$route->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>

<div class="mt-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Route steps</h5>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="add-route-step">Add step</button>
    </div>
    @error('steps') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
    <div class="route-steps-table-wrap">
        <table class="table table-sm route-steps-table" id="route-steps">
            <thead>
                <tr>
                    <th style="width: 4rem;">#</th>
                    <th style="width: 12rem;">Type</th>
                    <th>Location</th>
                    <th style="width: 4rem;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($steps as $index => $step)
                    @php
                        $pointType = $step['point_type'] ?? RoutePointType::Airport->value;
                    @endphp
                    <tr class="route-step-row">
                        <td class="align-middle text-muted step-number">{{ $index + 1 }}</td>
                        <td>
                            <select name="steps[{{ $index }}][point_type]" class="form-control form-control-sm route-point-type @error('steps.'.$index.'.point_type') is-invalid @enderror" required>
                                @foreach (RoutePointType::cases() as $type)
                                    <option value="{{ $type->value }}" @selected($pointType === $type->value)>{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="route-step-location">
                            <div class="route-step-airport @if($pointType !== RoutePointType::Airport->value) d-none @endif">
                                <select name="steps[{{ $index }}][airport_id]" class="form-control form-control-sm js-searchable-select @error('steps.'.$index.'.airport_id') is-invalid @enderror" data-dropdown-parent="body" @if($pointType === RoutePointType::Airport->value) required @endif>
                                    <option value="">Select airport</option>
                                    @foreach ($airports as $airport)
                                        <option value="{{ $airport->id }}" @selected((string) ($step['airport_id'] ?? '') === (string) $airport->id)>
                                            {{ $airport->name }} ({{ $airport->code }}) — {{ $airport->city->name ?? '—' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('steps.'.$index.'.airport_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="route-step-city @if($pointType !== RoutePointType::City->value) d-none @endif">
                                <select name="steps[{{ $index }}][city_id]" class="form-control form-control-sm js-searchable-select @error('steps.'.$index.'.city_id') is-invalid @enderror" data-dropdown-parent="body" @if($pointType === RoutePointType::City->value) required @endif>
                                    <option value="">Select city</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected((string) ($step['city_id'] ?? '') === (string) $city->id)>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('steps.'.$index.'.city_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="route-step-hajj @if($pointType !== RoutePointType::Hajj->value) d-none @endif">
                                <input type="text" class="form-control form-control-sm" value="Hajj" readonly tabindex="-1">
                            </div>
                        </td>
                        <td><button type="button" class="btn btn-outline-danger btn-sm remove-route-step">&times;</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="form-hint mb-0">Airport and city steps use Hajj Setup lists. Hajj is fixed for the pilgrimage leg.</p>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tableBody = document.querySelector('#route-steps tbody');
            var addButton = document.getElementById('add-route-step');
            var nextIndex = {{ count($steps) }};

            var airportOptions = `@foreach ($airports as $airport)<option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->code }}) — {{ $airport->city->name ?? '—' }}</option>@endforeach`;
            var cityOptions = `@foreach ($cities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach`;
            var pointTypeOptions = `@foreach (RoutePointType::cases() as $pointType)<option value="{{ $pointType->value }}">{{ $pointType->label() }}</option>@endforeach`;

            function renumberRows() {
                tableBody.querySelectorAll('.route-step-row').forEach(function (row, index) {
                    row.querySelector('.step-number').textContent = index + 1;
                });
            }

            function toggleStepLocation(row) {
                var type = row.querySelector('.route-point-type').value;
                var airportWrap = row.querySelector('.route-step-airport');
                var cityWrap = row.querySelector('.route-step-city');
                var hajjWrap = row.querySelector('.route-step-hajj');
                var airportSelect = row.querySelector('.route-step-airport select');
                var citySelect = row.querySelector('.route-step-city select');

                airportWrap.classList.toggle('d-none', type !== '{{ RoutePointType::Airport->value }}');
                cityWrap.classList.toggle('d-none', type !== '{{ RoutePointType::City->value }}');
                hajjWrap.classList.toggle('d-none', type !== '{{ RoutePointType::Hajj->value }}');

                airportSelect.required = type === '{{ RoutePointType::Airport->value }}';
                citySelect.required = type === '{{ RoutePointType::City->value }}';

                if (type !== '{{ RoutePointType::Airport->value }}') {
                    airportSelect.value = '';
                }

                if (type !== '{{ RoutePointType::City->value }}') {
                    citySelect.value = '';
                }
            }

            function bindRow(row) {
                row.querySelector('.route-point-type')?.addEventListener('change', function () {
                    toggleStepLocation(row);
                });
            }

            tableBody.querySelectorAll('.route-step-row').forEach(function (row) {
                bindRow(row);
                toggleStepLocation(row);
            });

            addButton?.addEventListener('click', function () {
                var row = document.createElement('tr');
                row.className = 'route-step-row';
                row.innerHTML = `
                    <td class="align-middle text-muted step-number">${nextIndex + 1}</td>
                    <td><select name="steps[${nextIndex}][point_type]" class="form-control form-control-sm route-point-type" required>${pointTypeOptions}</select></td>
                    <td class="route-step-location">
                        <div class="route-step-airport">
                            <select name="steps[${nextIndex}][airport_id]" class="form-control form-control-sm js-searchable-select" data-dropdown-parent="body"><option value="">Select airport</option>${airportOptions}</select>
                        </div>
                        <div class="route-step-city d-none">
                            <select name="steps[${nextIndex}][city_id]" class="form-control form-control-sm js-searchable-select" data-dropdown-parent="body"><option value="">Select city</option>${cityOptions}</select>
                        </div>
                        <div class="route-step-hajj d-none">
                            <input type="text" class="form-control form-control-sm" value="Hajj" readonly tabindex="-1">
                        </div>
                    </td>
                    <td><button type="button" class="btn btn-outline-danger btn-sm remove-route-step">&times;</button></td>
                `;
                tableBody.appendChild(row);
                bindRow(row);
                toggleStepLocation(row);
                if (window.AdminForm) {
                    window.AdminForm.initFormSelects(row);
                }
                nextIndex++;
                renumberRows();
            });

            tableBody?.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-route-step')) {
                    var rows = tableBody.querySelectorAll('.route-step-row');
                    if (rows.length > 2) {
                        event.target.closest('tr')?.remove();
                        renumberRows();
                    }
                }
            });
        });
    </script>
@endpush
