<div class="row g-3 align-items-end">
    <div class="col-lg-2 col-md-4">
        <label for="hajj_year" class="admin-form-label">Hajj Year</label>
        <select name="hajj_year" id="hajj_year" class="form-control">
            @foreach ($availableYears as $year)
                <option value="{{ $year }}" @selected((int) $filters['hajj_year'] === (int) $year)>
                    {{ $year }}@if ((int) $year === $activeYear) (Active) @endif
                </option>
            @endforeach
        </select>
    </div>
    <x-admin.filter-select name="direction" label="Journey" :selected="$filters['direction'] ?? ''" :searchable="false">
        @foreach ($filterOptions['directions'] as $direction)
            <option value="{{ $direction->value }}" @selected(($filters['direction'] ?? '') === $direction->value)>{{ $direction->label() }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.filter-select name="flight_type" label="Type" :selected="$filters['flight_type'] ?? ''" :searchable="false">
        @foreach ($filterOptions['flightTypes'] as $flightType)
            <option value="{{ $flightType->value }}" @selected(($filters['flight_type'] ?? '') === $flightType->value)>{{ $flightType->label() }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.filter-select name="flight_id" label="Flight" :selected="$filters['flight_id'] ?? ''">
        @foreach ($filterOptions['flights'] as $flight)
            <option value="{{ $flight->id }}" @selected((string) ($filters['flight_id'] ?? '') === (string) $flight->id)>{{ $flight->reportFilterLabel() }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.filter-select name="company_id" label="Company" :selected="$filters['company_id'] ?? ''">
        @foreach ($filterOptions['companies'] as $company)
            <option value="{{ $company->id }}" @selected((string) ($filters['company_id'] ?? '') === (string) $company->id)>{{ $company->registrationOptionLabel() }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.package-select
        :packages="$filterOptions['packages']"
        :selected="$filters['package_id'] ?? ''"
        filter-mode
    />
    <div class="col-lg-2 col-md-4">
        <label for="departure_from" class="admin-form-label">Departure From</label>
        <x-admin.date-input name="departure_from" id="departure_from" :value="$filters['departure_from'] ?? ''" />
    </div>
    <div class="col-lg-2 col-md-4">
        <label for="departure_to" class="admin-form-label">Departure To</label>
        <x-admin.date-input name="departure_to" id="departure_to" :value="$filters['departure_to'] ?? ''" />
    </div>
    <div class="col-lg-3 col-md-6">
        <label for="search" class="admin-form-label">Search</label>
        <input type="text" name="search" id="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Name, passport, family, flight no">
    </div>
    <div class="col-lg-auto d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm" data-report-generate>Generate</button>
        @if ($hasFilters)
            <a href="{{ route('admin.reports.show', $reportKey) }}" class="btn btn-outline-secondary btn-sm">Clear</a>
        @endif
    </div>
</div>
