@extends('layouts.app')

@section('page-title', 'Dashboard')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
@endpush

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
                                <span class="deyar-quota-stat-card__label">Total</span>
                                <span class="deyar-quota-stat-card__value">{{ number_format($quotaStats['total_quota']) }}</span>
                            </div>
                            <div class="deyar-quota-stat-card deyar-quota-stat-card--utilised">
                                <span class="deyar-quota-stat-card__label">Utilised</span>
                                <span class="deyar-quota-stat-card__value">{{ number_format($quotaStats['utilised']) }}</span>
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

                            @if ($quotaStats['unlimited_companies'] > 0)
                                <p class="deyar-dashboard-note deyar-quota-overview__note mb-0">
                                    {{ $quotaStats['unlimited_companies'] }} {{ str('company')->plural($quotaStats['unlimited_companies']) }} with unlimited quota excluded from totals.
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

    @if ($pilgrimStats || $quotaStats)
        <div class="row g-3 mb-3">
            @if ($pilgrimStats)
                <div class="{{ $quotaStats ? 'col-xl-8' : 'col-xl-12' }}">
                    <div class="card h-100 deyar-panel-card">
                        <div class="card-header">
                            <h6 class="deyar-panel-card__title">Registrations (Last 6 Months)</h6>
                        </div>
                        <div class="card-body">
                            <div class="deyar-chart-wrap" id="trendChartWrap">
                                <canvas id="trendChart" height="100"></canvas>
                                <div class="deyar-empty-state d-none" id="trendChartEmpty">No registration data for the last 6 months.</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($quotaStats)
                <div class="{{ $pilgrimStats ? 'col-xl-4' : 'col-xl-12' }}">
                    <div class="card h-100 deyar-panel-card">
                        <div class="card-header">
                            <h6 class="deyar-panel-card__title mb-0">Company Quota Utilisation</h6>
                        </div>
                        <div class="card-body">
                            @forelse ($quotaStats['companies'] as $company)
                                <div class="deyar-quota-item">
                                    <div class="deyar-quota-item__header">
                                        <span class="deyar-quota-item__name">
                                            {{ $company['name'] }}
                                            @if ($company['code'])
                                                <span class="text-muted">({{ $company['code'] }})</span>
                                            @endif
                                        </span>
                                        <span class="deyar-quota-item__meta">
                                            {{ number_format($company['used']) }}/{{ number_format($company['quota']) }}
                                        </span>
                                    </div>
                                    <div class="deyar-quota-progress" role="progressbar"
                                         aria-valuenow="{{ $company['percentage'] }}" aria-valuemin="0" aria-valuemax="100"
                                         aria-label="{{ $company['name'] }} quota utilisation">
                                        <div class="deyar-quota-progress__fill {{ $company['percentage'] >= 100 ? 'deyar-quota-progress__fill--danger' : ($company['percentage'] >= 80 ? 'deyar-quota-progress__fill--warning' : '') }}"
                                             style="width: {{ $company['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="deyar-empty-state border-0">No company quotas configured yet.</div>
                            @endforelse
                        </div>
                    </div>
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

@push('scripts')
<script>
    (function () {
        const root = document.documentElement;
        const cssVar = (name) => getComputedStyle(root).getPropertyValue(name).trim();
        const chartPrimary = cssVar('--deyar-chart-1') || '#B8956A';

        function showEmptyState(canvasId, emptyId) {
            const canvas = document.getElementById(canvasId);
            const empty = document.getElementById(emptyId);

            if (canvas) {
                canvas.classList.add('d-none');
            }

            if (empty) {
                empty.classList.remove('d-none');
            }
        }

        function makeTrendChart(id, emptyId, dataset) {
            const canvas = document.getElementById(id);

            if (!canvas) {
                return;
            }

            if (!dataset.length || dataset.every((row) => !row.total)) {
                showEmptyState(id, emptyId);

                return;
            }

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: dataset.map((d) => d.label ?? 'Unknown'),
                    datasets: [{
                        data: dataset.map((d) => d.total),
                        backgroundColor: 'rgba(184, 149, 106, 0.12)',
                        borderColor: chartPrimary,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: chartPrimary,
                        pointRadius: 3,
                    }],
                },
                options: {
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#6b7280' },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 }, color: '#6b7280' },
                            grid: { color: '#f3f4f6' },
                        },
                    },
                },
            });
        }

        makeTrendChart('trendChart', 'trendChartEmpty', @json(data_get($pilgrimStats, 'monthly_trend', [])));
    })();
</script>
@endpush
