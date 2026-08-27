@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="deyar-dashboard">

    <div class="row g-3 mb-3">
        @if ($quotaStats)
            <div class="col-12 {{ $pilgrimStats ? 'col-xl-8' : 'col-xl-12' }}">
                <div class="card h-100 deyar-metric-card deyar-quota-overview">
                    <div class="card-body">
                        <div class="deyar-quota-overview__header">
                            <h6 class="deyar-dashboard-card__title">Quota Overview</h6>
                            <span class="deyar-quota-overview__year">Hajj {{ $hajjYear }}</span>
                        </div>

                        <div class="deyar-quota-overview__stats">
                            <div class="deyar-quota-stat-card deyar-quota-stat-card--total">
                                <span class="deyar-quota-stat-card__label">Total Quota</span>
                                <span class="deyar-quota-stat-card__value">{{ number_format($quotaStats['total_quota']) }}</span>
                            </div>
                            <div class="deyar-quota-stat-card deyar-quota-stat-card--entered">
                                <span class="deyar-quota-stat-card__label">Entered</span>
                                <span class="deyar-quota-stat-card__value">{{ number_format($quotaStats['entered']) }}</span>
                            </div>
                            <div class="deyar-quota-stat-card deyar-quota-stat-card--remaining">
                                <span class="deyar-quota-stat-card__label">Remaining</span>
                                <span class="deyar-quota-stat-card__value">{{ number_format($quotaStats['remaining']) }}</span>
                            </div>
                        </div>

                        <div class="deyar-quota-overview__footer">
                            <div class="deyar-quota-overview__progress-header">
                                <span class="deyar-quota-overview__progress-label">Overall utilisation</span>
                                <span class="deyar-quota-overview__progress-value">{{ $quotaStats['utilisation_percentage'] }}%</span>
                            </div>
                            <div class="deyar-quota-progress" role="progressbar"
                                 aria-valuenow="{{ $quotaStats['utilisation_percentage'] }}" aria-valuemin="0" aria-valuemax="100"
                                 aria-label="Overall quota utilisation">
                                <div class="deyar-quota-progress__fill {{ $quotaStats['utilisation_percentage'] >= 100 ? 'deyar-quota-progress__fill--danger' : ($quotaStats['utilisation_percentage'] >= 80 ? 'deyar-quota-progress__fill--warning' : '') }}"
                                     style="width: {{ $quotaStats['utilisation_percentage'] }}%"></div>
                            </div>

                            @if ($quotaStats['unlimited_count'] > 0)
                                <p class="deyar-dashboard-note deyar-quota-overview__note mb-0">
                                    {{ $quotaStats['unlimited_count'] }} {{ str($quotaStats['unlimited_label'])->plural($quotaStats['unlimited_count']) }} with unlimited quota excluded from totals.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($pilgrimStats)
            <div class="col-12 {{ $quotaStats ? 'col-xl-4' : 'col-xl-12' }}">
                <div class="card h-100 deyar-metric-card deyar-registration-card">
                    <div class="card-body d-flex flex-column">
                        <h6 class="deyar-dashboard-card__title">Hajj {{ $pilgrimStats['hajj_year'] }} Registrations</h6>
                        <span class="deyar-registration-card__value">{{ number_format($pilgrimStats['this_year']) }}</span>
                        @can('pilgrims.create')
                            <a href="{{ route('admin.pilgrims.create') }}" class="btn btn-primary btn-sm w-100 mt-auto">
                                New Registration
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($quotaStats || $packageStats || $formOwnerStats)
        <div class="row g-3 mb-3">
            @if ($quotaStats)
                <div class="col-12 {{ ($packageStats || $formOwnerStats) ? 'col-xl-4' : 'col-xl-12' }}">
                    @include('partials.dashboard-utilisation-panel', [
                        'title' => 'Company Quota Utilisation',
                        'stats' => $quotaStats,
                        'emptyMessage' => 'No company quotas configured yet.',
                    ])
                </div>
            @endif

            @if ($packageStats)
                <div class="col-12 {{ ($quotaStats && $formOwnerStats) ? 'col-xl-4' : (($quotaStats || $formOwnerStats) ? 'col-xl-6' : 'col-xl-12') }}">
                    @include('partials.dashboard-utilisation-panel', [
                        'title' => 'Package Limit Utilisation',
                        'stats' => $packageStats,
                        'emptyMessage' => 'No package limits configured yet.',
                    ])
                </div>
            @endif

            @if ($formOwnerStats)
                <div class="col-12 {{ ($quotaStats && $packageStats) ? 'col-xl-4' : (($quotaStats || $packageStats) ? 'col-xl-6' : 'col-xl-12') }}">
                    @include('partials.dashboard-utilisation-panel', [
                        'title' => 'Form Owner Limit Utilisation',
                        'stats' => $formOwnerStats,
                        'emptyMessage' => 'No form owner limits configured yet.',
                    ])
                </div>
            @endif
        </div>
    @endif

    @if ($pilgrimStats)
        <div class="card deyar-panel-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="deyar-panel-card__title mb-0">Recent Registrations</h6>
                @can('pilgrims.view')
                    <a href="{{ route('admin.pilgrims.index') }}" class="deyar-dashboard-link">View all</a>
                @endcan
            </div>
            <div class="card-body p-0">
                @if ($pilgrimStats['recent']->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 deyar-dashboard-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Family Code</th>
                                    <th>Passport</th>
                                    <th>Company</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pilgrimStats['recent'] as $pilgrim)
                                    <tr>
                                        <td class="fw-medium">{{ $pilgrim->full_name ?: '—' }}</td>
                                        <td>{{ $pilgrim->family_code ?: '—' }}</td>
                                        <td>{{ $pilgrim->passport_no ?: '—' }}</td>
                                        <td>{{ $pilgrim->company?->name ?: '—' }}</td>
                                        <td>
                                            <a href="{{ route('admin.pilgrims.show', $pilgrim) }}" class="btn btn-outline-info btn-xs">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="deyar-empty-state border-0 rounded-0">No registrations yet.</div>
                @endif
            </div>
        </div>
    @endif

    @if ($flightStats)
        <div class="card deyar-panel-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="deyar-panel-card__title mb-0">Upcoming Flights</h6>
                @can('flights.view')
                    <a href="{{ route('admin.flights.index') }}" class="deyar-dashboard-link">View all</a>
                @endcan
            </div>
            <div class="card-body">
                @forelse ($flightStats['upcoming'] as $flight)
                    <div class="deyar-list-item">
                        <span>{{ $flight->direction->label() }}: {{ $flight->departureCity?->name }} → {{ $flight->arrivalCity?->name }}</span>
                        <span class="deyar-list-item__meta">{{ number_format($flight->pilgrims_count) }} hujaj · {{ $flight->departure_date?->format('d M Y') }}</span>
                    </div>
                @empty
                    <div class="deyar-empty-state">No upcoming flights scheduled.</div>
                @endforelse
            </div>
        </div>
    @endif

</div>
@endsection
