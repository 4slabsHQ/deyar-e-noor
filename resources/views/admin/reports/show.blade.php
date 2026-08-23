@extends('layouts.app')

@section('title', $reportLabel)
@section('page-title', $reportLabel)

@section('content')
    @push('styles')
        <link href="{{ asset('css/pilgrim-form.css') }}" rel="stylesheet">
    @endpush

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

        <form method="GET" action="{{ route('admin.reports.show', $reportKey) }}" id="report-builder-form" class="admin-form pilgrim-form">
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
                    <div class="report-columns-panel">
                        @foreach ($columnGroups as $group => $columns)
                            <div class="report-column-group">
                                <div class="report-column-group-title">{{ $group }}</div>
                                <div class="report-columns-grid">
                                    @foreach ($columns as $column)
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
            @if ($resultView)
                @include('admin.reports._results', $resultView)
            @endif
        </div>

        <div class="modal fade" id="report-title-modal" tabindex="-1" aria-labelledby="report-title-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="report-title-modal-label">Report Title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="report-title-input" class="admin-form-label">Title to show on the report</label>
                        <input type="text" id="report-title-input" class="form-control" maxlength="150" placeholder="Enter report title">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm" id="report-title-confirm">Continue</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/reports.js') }}?v=16"></script>
@endpush
