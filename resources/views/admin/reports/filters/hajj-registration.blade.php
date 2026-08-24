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
            <option value="{{ $company->id }}" @selected((string) ($filters['company_id'] ?? '') === (string) $company->id)>{{ $company->name }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.package-select
        :packages="$filterOptions['packages']"
        :selected="$filters['package_id'] ?? ''"
        filter-mode
    />
    <x-admin.filter-select name="maktab_category_id" label="Maktab" :selected="$filters['maktab_category_id'] ?? ''">
        @foreach ($filterOptions['maktabCategories'] as $maktab)
            <option value="{{ $maktab->id }}" @selected((string) ($filters['maktab_category_id'] ?? '') === (string) $maktab->id)>{{ $maktab->name }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.filter-select name="form_owner_id" label="Form Owner" :selected="$filters['form_owner_id'] ?? ''">
        @foreach ($filterOptions['formOwners'] as $formOwner)
            <option value="{{ $formOwner->id }}" @selected((string) ($filters['form_owner_id'] ?? '') === (string) $formOwner->id)>{{ $formOwner->name }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.filter-select name="pod_city_id" label="POD" :selected="$filters['pod_city_id'] ?? ''">
        @foreach ($filterOptions['podCities'] as $city)
            <option value="{{ $city->id }}" @selected((string) ($filters['pod_city_id'] ?? '') === (string) $city->id)>{{ $city->name }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.filter-select name="care_off_id" label="Care Off" :selected="$filters['care_off_id'] ?? ''">
        @foreach ($filterOptions['careOffs'] as $careOff)
            <option value="{{ $careOff->id }}" @selected((string) ($filters['care_off_id'] ?? '') === (string) $careOff->id)>{{ $careOff->name }}</option>
        @endforeach
    </x-admin.filter-select>
    <x-admin.filter-select name="gender" label="Gender" :selected="$filters['gender'] ?? ''" :searchable="false">
        @foreach (\App\Enums\Gender::cases() as $gender)
            <option value="{{ $gender->value }}" @selected(($filters['gender'] ?? '') === $gender->value)>{{ $gender->label() }}</option>
        @endforeach
    </x-admin.filter-select>
    <div class="col-lg-2 col-md-4">
        <label for="entry_from" class="admin-form-label">Entry From</label>
        <x-admin.date-input name="entry_from" id="entry_from" :value="$filters['entry_from'] ?? ''" />
    </div>
    <div class="col-lg-2 col-md-4">
        <label for="entry_to" class="admin-form-label">Entry To</label>
        <x-admin.date-input name="entry_to" id="entry_to" :value="$filters['entry_to'] ?? ''" />
    </div>
    <div class="col-lg-3 col-md-6">
        <label for="search" class="admin-form-label">Search</label>
        <input type="text" name="search" id="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Name, passport, CNIC, family, mobile">
    </div>
    <div class="col-lg-auto d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm" data-report-generate>Generate</button>
        @if ($hasFilters)
            <a href="{{ route('admin.reports.show', $reportKey) }}" class="btn btn-outline-secondary btn-sm">Clear</a>
        @endif
    </div>
</div>
