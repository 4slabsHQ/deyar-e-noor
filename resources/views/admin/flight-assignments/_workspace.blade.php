<div class="flight-assignment-workspace"
     data-flight-id="{{ $flight->id }}"
     data-workspace-url="{{ $workspaceUrl }}"
     data-results-url="{{ $resultsUrl }}">
    <div class="card admin-index-card mb-4 flight-assignment-filters-card">
        <div class="card-header">
            <h4 class="card-title mb-0">Filters</h4>
        </div>
        <div class="card-body">
            <div class="flight-assignment-context mb-3 pb-3 border-bottom">
                <p class="fw-medium mb-1">{{ $flight->direction->label() }} · {{ $flight->departure_flight_no }}</p>
                <p class="text-muted mb-0">
                    {{ $flight->departureCity?->name }} ({{ $flight->departureAirport?->code }})
                    →
                    {{ $flight->arrivalCity?->name }} ({{ $flight->arrivalAirport?->code }})
                    · {{ $flight->flight_type->label() }}
                    · {{ $flight->departure_date?->format('d M Y') }}
                    · Season {{ $activeYear }}
                    · <span data-flight-pilgrims-count>{{ number_format($flight->pilgrims_count) }}</span> assigned
                </p>
            </div>

            <form method="GET"
                  action="{{ $workspaceUrl }}"
                  class="admin-form flight-assignment-filter-form mb-0"
                  data-workspace-filter-form>
                <div class="row g-3 align-items-end">
                    <x-admin.filter-select
                        name="company_id"
                        label="Company"
                        :selected="$filters['company_id'] ?? ''"
                        id="company_id_{{ $flight->id }}"
                    >
                        @foreach ($filterOptions['companies'] as $company)
                            <option value="{{ $company->id }}" @selected((string) ($filters['company_id'] ?? '') === (string) $company->id)>{{ $company->registrationOptionLabel() }}</option>
                        @endforeach
                    </x-admin.filter-select>
                    <x-admin.filter-select
                        name="pod_city_id"
                        label="POD"
                        :selected="$filters['pod_city_id'] ?? ''"
                        id="pod_city_id_{{ $flight->id }}"
                    >
                        @foreach ($filterOptions['podCities'] as $city)
                            <option value="{{ $city->id }}" @selected((string) ($filters['pod_city_id'] ?? '') === (string) $city->id)>{{ $city->name }}</option>
                        @endforeach
                    </x-admin.filter-select>
                    <x-admin.package-select
                        :packages="$filterOptions['packages']"
                        :selected="$filters['package_id'] ?? ''"
                        id="package_id_{{ $flight->id }}"
                        filter-mode
                    />
                    <x-admin.filter-select
                        name="form_owner_id"
                        label="Form Owner"
                        :selected="$filters['form_owner_id'] ?? ''"
                        id="form_owner_id_{{ $flight->id }}"
                    >
                        @foreach ($filterOptions['formOwners'] as $formOwner)
                            <option value="{{ $formOwner->id }}" @selected((string) ($filters['form_owner_id'] ?? '') === (string) $formOwner->id)>{{ $formOwner->name }}</option>
                        @endforeach
                    </x-admin.filter-select>
                    <div class="col-lg-2 col-md-4">
                        <label for="family_code_{{ $flight->id }}" class="admin-form-label">Family Code</label>
                        <input type="text" name="family_code" id="family_code_{{ $flight->id }}" class="form-control" value="{{ $filters['family_code'] ?? '' }}" placeholder="e.g. DYN-01-A">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label for="search_{{ $flight->id }}" class="admin-form-label">Search</label>
                        <input type="text" name="search" id="search_{{ $flight->id }}" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Name, passport, family">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label for="assignment_status_{{ $flight->id }}" class="admin-form-label">On this flight</label>
                        <select name="assignment_status" id="assignment_status_{{ $flight->id }}" class="form-control">
                            <option value="all" @selected(($filters['assignment_status'] ?? 'all') === 'all')>All</option>
                            <option value="on_flight" @selected(($filters['assignment_status'] ?? '') === 'on_flight')>Yes</option>
                            <option value="not_on_flight" @selected(($filters['assignment_status'] ?? '') === 'not_on_flight')>No</option>
                        </select>
                    </div>
                    <div class="col-lg-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm" data-workspace-apply-filters>Apply</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-workspace-clear-filters>Clear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card admin-index-card flight-assignment-results-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0">Hujaj</h4>
                <span class="text-muted small" data-flight-results-count>{{ number_format($pilgrims->count()) }} rows</span>
            </div>
        </div>
        <div class="card-body" id="flight-assignment-results">
            @include('admin.flight-assignments._workspace-results')
        </div>
    </div>
</div>
