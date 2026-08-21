<div class="flight-assignment-workspace" data-flight-id="{{ $flight->id }}" data-workspace-url="{{ $workspaceUrl }}">
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
          class="admin-form mb-3 flight-assignment-filter-form"
          data-workspace-filter-form>
        <div class="row g-3 align-items-end">
            <div class="col-lg-2 col-md-4">
                <label for="company_id_{{ $flight->id }}" class="admin-form-label">Company</label>
                <select name="company_id" id="company_id_{{ $flight->id }}" class="form-control">
                    <option value="">All</option>
                    @foreach ($filterOptions['companies'] as $company)
                        <option value="{{ $company->id }}" @selected((string) ($filters['company_id'] ?? '') === (string) $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label for="pod_city_id_{{ $flight->id }}" class="admin-form-label">POD</label>
                <select name="pod_city_id" id="pod_city_id_{{ $flight->id }}" class="form-control">
                    <option value="">All</option>
                    @foreach ($filterOptions['podCities'] as $city)
                        <option value="{{ $city->id }}" @selected((string) ($filters['pod_city_id'] ?? '') === (string) $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label for="package_id_{{ $flight->id }}" class="admin-form-label">Package</label>
                <select name="package_id" id="package_id_{{ $flight->id }}" class="form-control">
                    <option value="">All</option>
                    @foreach ($filterOptions['packages'] as $package)
                        <option value="{{ $package->id }}" @selected((string) ($filters['package_id'] ?? '') === (string) $package->id)>{{ $package->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label for="form_owner_id_{{ $flight->id }}" class="admin-form-label">Form Owner</label>
                <select name="form_owner_id" id="form_owner_id_{{ $flight->id }}" class="form-control">
                    <option value="">All</option>
                    @foreach ($filterOptions['formOwners'] as $formOwner)
                        <option value="{{ $formOwner->id }}" @selected((string) ($filters['form_owner_id'] ?? '') === (string) $formOwner->id)>{{ $formOwner->name }}</option>
                    @endforeach
                </select>
            </div>
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
                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-workspace-clear-filters>Clear</button>
            </div>
        </div>
    </form>

    <form method="POST"
          action="{{ route('admin.flight-assignments.store', $flight) }}"
          class="flight-assignment-bulk-form"
          id="flight-assignment-bulk-form">
        @csrf
        <input type="hidden" name="action" value="assign" id="bulk-action-input">
        <input type="hidden" name="select_all" value="0" class="select-all-input">
        @foreach ($filters as $key => $value)
            @if(filled($value))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        @include('admin.flight-assignments._workspace-table')
    </form>
</div>
