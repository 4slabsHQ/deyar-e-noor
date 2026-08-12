@extends('layouts.app')

@section('page-title', 'Dashboard')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="deyar-dashboard">

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 deyar-metric-card">
                <div class="card-body">
                    <span class="deyar-metric__label">Signed in as</span>
                    <span class="deyar-metric__value deyar-metric__value--text">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>

        @if ($pilgrimStats)
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 deyar-metric-card">
                    <div class="card-body">
                        <span class="deyar-metric__label">Total Registrations</span>
                        <span class="deyar-metric__value">{{ number_format($pilgrimStats['total']) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 deyar-metric-card">
                    <div class="card-body">
                        <span class="deyar-metric__label">Hajj {{ now()->year }}</span>
                        <span class="deyar-metric__value">{{ number_format($pilgrimStats['this_year']) }}</span>
                    </div>
                </div>
            </div>
            @can('pilgrims.create')
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100 deyar-dashboard-action deyar-metric-card">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <a href="{{ route('admin.pilgrims.create') }}" class="btn btn-primary btn-sm">
                                New Registration
                            </a>
                        </div>
                    </div>
                </div>
            @endcan
        @endif

        @if ($flightStats)
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 deyar-metric-card">
                    <div class="card-body">
                        <span class="deyar-metric__label">Total Flights</span>
                        <span class="deyar-metric__value">{{ number_format($flightStats['total']) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($pilgrimStats)
        <div class="row g-3 mb-3">
            <div class="col-xl-6">
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
            <div class="col-xl-3">
                <div class="card h-100 deyar-panel-card">
                    <div class="card-header">
                        <h6 class="deyar-panel-card__title">Gender</h6>
                    </div>
                    <div class="card-body">
                        <div class="deyar-chart-wrap" id="genderChartWrap">
                            <canvas id="genderChart" height="100"></canvas>
                            <div class="deyar-empty-state d-none" id="genderChartEmpty">No gender breakdown yet.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card h-100 deyar-panel-card">
                    <div class="card-header">
                        <h6 class="deyar-panel-card__title">Top Packages</h6>
                    </div>
                    <div class="card-body">
                        <div class="deyar-chart-wrap" id="packageChartWrap">
                            <canvas id="packageChart" height="100"></canvas>
                            <div class="deyar-empty-state d-none" id="packageChartEmpty">No package data yet.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card deyar-panel-card mb-3">
            <div class="card-header">
                <h6 class="deyar-panel-card__title">Recent Registrations</h6>
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
                                    <th>Hajj Year</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pilgrimStats['recent'] as $pilgrim)
                                    <tr>
                                        <td class="fw-medium">{{ $pilgrim->full_name }}</td>
                                        <td>{{ $pilgrim->family_code }}</td>
                                        <td>{{ $pilgrim->passport_no }}</td>
                                        <td>{{ $pilgrim->hajj_year }}</td>
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
        <div class="row g-3">
            <div class="col-xl-6">
                <div class="card h-100 deyar-panel-card">
                    <div class="card-header">
                        <h6 class="deyar-panel-card__title">Flights by Airline</h6>
                    </div>
                    <div class="card-body">
                        <div class="deyar-chart-wrap" id="airlineChartWrap">
                            <canvas id="airlineChart" height="100"></canvas>
                            <div class="deyar-empty-state d-none" id="airlineChartEmpty">No flight data by airline yet.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card h-100 deyar-panel-card">
                    <div class="card-header">
                        <h6 class="deyar-panel-card__title">Upcoming Departures</h6>
                    </div>
                    <div class="card-body">
                        @forelse ($flightStats['upcoming'] as $flight)
                            <div class="deyar-list-item">
                                <span>{{ $flight->departureCity?->name }} → {{ $flight->arrivalCity?->name }}</span>
                                <span class="deyar-list-item__meta">{{ $flight->departure_date?->format('d M Y') }}</span>
                            </div>
                        @empty
                            <div class="deyar-empty-state">No upcoming flights scheduled.</div>
                        @endforelse
                    </div>
                </div>
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

        const chartPalette = [
            cssVar('--deyar-chart-1'),
            cssVar('--deyar-chart-2'),
            cssVar('--deyar-chart-3'),
            cssVar('--deyar-chart-4'),
            cssVar('--deyar-chart-5'),
            cssVar('--deyar-chart-6'),
        ].filter(Boolean);

        const chartPrimary = chartPalette[0] || '#B8956A';

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

        function makeChart(id, emptyId, type, dataset, extraOptions = {}) {
            const canvas = document.getElementById(id);

            if (!canvas) {
                return;
            }

            if (!dataset.length || dataset.every((row) => !row.total)) {
                showEmptyState(id, emptyId);

                return;
            }

            const isLine = type === 'line';

            new Chart(canvas, {
                type,
                data: {
                    labels: dataset.map((d) => d.label ?? 'Unknown'),
                    datasets: [{
                        data: dataset.map((d) => d.total),
                        backgroundColor: isLine ? 'rgba(184, 149, 106, 0.12)' : chartPalette,
                        borderColor: isLine ? chartPrimary : chartPalette,
                        borderWidth: isLine ? 2 : 1,
                        fill: isLine,
                        tension: 0.3,
                        pointBackgroundColor: chartPrimary,
                        pointRadius: isLine ? 3 : 0,
                    }],
                },
                options: {
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: type === 'doughnut',
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                font: { size: 11 },
                            },
                        },
                    },
                    scales: type === 'bar' || isLine ? {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#6b7280' },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 }, color: '#6b7280' },
                            grid: { color: '#f3f4f6' },
                        },
                    } : {},
                    ...extraOptions,
                },
            });
        }

        makeChart('trendChart', 'trendChartEmpty', 'line', @json(data_get($pilgrimStats, 'monthly_trend', [])));
        makeChart('genderChart', 'genderChartEmpty', 'doughnut', @json(data_get($pilgrimStats, 'by_gender', [])));
        makeChart('packageChart', 'packageChartEmpty', 'bar', @json(data_get($pilgrimStats, 'by_package', [])));
        makeChart('airlineChart', 'airlineChartEmpty', 'bar', @json(data_get($flightStats, 'by_airline', [])));
    })();
</script>
@endpush
