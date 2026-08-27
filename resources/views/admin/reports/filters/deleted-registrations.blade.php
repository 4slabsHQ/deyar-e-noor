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
    <x-admin.filter-select name="company_id" label="Company" :selected="$filters['company_id'] ?? ''">
        @foreach ($filterOptions['companies'] as $company)
            <option value="{{ $company->id }}" @selected((string) ($filters['company_id'] ?? '') === (string) $company->id)>{{ $company->registrationOptionLabel() }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.filter-select name="deleted_by" label="Deleted By" :selected="$filters['deleted_by'] ?? ''">
        @foreach ($filterOptions['deleters'] as $deleter)
            <option value="{{ $deleter->id }}" @selected((string) ($filters['deleted_by'] ?? '') === (string) $deleter->id)>{{ $deleter->name }}</option>
        @endforeach
    </x-admin.filter-select>
    <div class="col-lg-2 col-md-4">
        <label for="deleted_from" class="admin-form-label">Deleted From</label>
        <x-admin.date-input name="deleted_from" id="deleted_from" :value="$filters['deleted_from'] ?? ''" />
    </div>
    <div class="col-lg-2 col-md-4">
        <label for="deleted_to" class="admin-form-label">Deleted To</label>
        <x-admin.date-input name="deleted_to" id="deleted_to" :value="$filters['deleted_to'] ?? ''" />
    </div>
    <div class="col-lg-3 col-md-6">
        <label for="search" class="admin-form-label">Search</label>
        <input type="text" name="search" id="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Name, passport, family code">
    </div>
    <div class="col-lg-auto d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm" data-report-generate>Generate</button>
        @if ($hasFilters)
            <a href="{{ route('admin.reports.show', $reportKey) }}" class="btn btn-outline-secondary btn-sm">Clear</a>
        @endif
    </div>
</div>
