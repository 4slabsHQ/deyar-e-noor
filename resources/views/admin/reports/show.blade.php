@extends('layouts.app')

@section('title', $reportLabel)
@section('page-title', $reportLabel)

@section('content')
    <div class="admin-index-page reports-page"
         id="reports-page"
         data-results-url="{{ route('admin.reports.results', $reportKey) }}">
        <x-admin.validation-alert />

        <div id="report-validation-alert"></div>

        @if (session('status') === 'column-defaults-saved')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                Column selection saved as your default.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-index-header mb-4">
            <h4 class="admin-index-title mb-0">{{ $reportLabel }}</h4>
        </div>

        <form method="GET" action="{{ route('admin.reports.show', $reportKey) }}" id="report-builder-form">
            <input type="hidden" name="run" value="1">

            <div class="card admin-index-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="card-title mb-0">Columns</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-report-select-all-columns>Select all</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-report-clear-columns>Clear all</button>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-report-save-defaults>Save as default</button>
                    </div>
                </div>
                <div class="card-body pt-3 pb-3">
                    <div class="report-columns-grid">
                        @foreach ($columnOptions as $column)
                            <div class="form-check report-column-check">
                                <input type="checkbox"
                                       class="form-check-input report-column-checkbox"
                                       name="columns[]"
                                       value="{{ $column['key'] }}"
                                       id="column-{{ $column['key'] }}"
                                       @checked(in_array($column['key'], $selectedColumns, true))>
                                <label class="form-check-label" for="column-{{ $column['key'] }}">{{ $column['label'] }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card admin-index-card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Filters</h4>
                </div>
                <div class="card-body">
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
                        <div class="col-lg-2 col-md-4">
                            <label for="company_id" class="admin-form-label">Company</label>
                            <select name="company_id" id="company_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($filterOptions['companies'] as $company)
                                    <option value="{{ $company->id }}" @selected((string) ($filters['company_id'] ?? '') === (string) $company->id)>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label for="package_id" class="admin-form-label">Package</label>
                            <select name="package_id" id="package_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($filterOptions['packages'] as $package)
                                    <option value="{{ $package->id }}" @selected((string) ($filters['package_id'] ?? '') === (string) $package->id)>{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label for="maktab_category_id" class="admin-form-label">Maktab</label>
                            <select name="maktab_category_id" id="maktab_category_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($filterOptions['maktabCategories'] as $maktab)
                                    <option value="{{ $maktab->id }}" @selected((string) ($filters['maktab_category_id'] ?? '') === (string) $maktab->id)>{{ $maktab->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label for="form_owner_id" class="admin-form-label">Form Owner</label>
                            <select name="form_owner_id" id="form_owner_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($filterOptions['formOwners'] as $formOwner)
                                    <option value="{{ $formOwner->id }}" @selected((string) ($filters['form_owner_id'] ?? '') === (string) $formOwner->id)>{{ $formOwner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label for="pod_city_id" class="admin-form-label">POD</label>
                            <select name="pod_city_id" id="pod_city_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($filterOptions['podCities'] as $city)
                                    <option value="{{ $city->id }}" @selected((string) ($filters['pod_city_id'] ?? '') === (string) $city->id)>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label for="care_off_id" class="admin-form-label">Care Off</label>
                            <select name="care_off_id" id="care_off_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($filterOptions['careOffs'] as $careOff)
                                    <option value="{{ $careOff->id }}" @selected((string) ($filters['care_off_id'] ?? '') === (string) $careOff->id)>{{ $careOff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label for="gender" class="admin-form-label">Gender</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="">All</option>
                                @foreach (\App\Enums\Gender::cases() as $gender)
                                    <option value="{{ $gender->value }}" @selected(($filters['gender'] ?? '') === $gender->value)>{{ $gender->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label for="entry_from" class="admin-form-label">Entry From</label>
                            <input type="date" name="entry_from" id="entry_from" class="form-control" value="{{ $filters['entry_from'] ?? '' }}">
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label for="entry_to" class="admin-form-label">Entry To</label>
                            <input type="date" name="entry_to" id="entry_to" class="form-control" value="{{ $filters['entry_to'] ?? '' }}">
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
                </div>
            </div>
        </form>

        <form method="POST"
              action="{{ route('admin.reports.columns.save', $reportKey) }}"
              id="report-save-columns-form"
              class="d-none">
            @csrf
            <div id="report-save-columns-inputs"></div>
        </form>

        <div id="report-results">
            @if ($result)
                @include('admin.reports._results', [
                    'result' => $result,
                    'exportQuery' => $exportQuery,
                    'reportLabel' => $reportLabel,
                ])
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/reports.js') }}?v=14"></script>
@endpush
