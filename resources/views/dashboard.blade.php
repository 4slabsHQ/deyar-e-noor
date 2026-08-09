@extends('layouts.app')

@section('page-title', 'Dashboard')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
@endpush

@section('content')

<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <span class="deyar-metric__label">Signed in as</span>
                <span class="deyar-metric__value deyar-metric__value--text">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>

    @if ($pilgrimStats)
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <span class="deyar-metric__label">Total Registrations</span>
                    <span class="deyar-metric__value">{{ number_format($pilgrimStats['total']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <span class="deyar-metric__label">Hajj {{ now()->year }}</span>
                    <span class="deyar-metric__value">{{ number_format($pilgrimStats['this_year']) }}</span>
                </div>
            </div>
        </div>
        @can('pilgrims.create')
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 deyar-dashboard-action">
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
            <div class="card h-100">
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
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-3">Registrations (Last 6 Months)</h6>
                    <canvas id="trendChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-3">Gender</h6>
                    <canvas id="genderChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-3">Top Packages</h6>
                    <canvas id="packageChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if ($pilgrimStats['recent']->isNotEmpty())
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-3">Recent Registrations</h6>
                <table class="table table-sm mb-0">
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
                                <td>{{ $pilgrim->full_name }}</td>
                                <td>{{ $pilgrim->family_code }}</td>
                                <td>{{ $pilgrim->passport_no }}</td>
                                <td>{{ $pilgrim->hajj_year }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.pilgrims.show', $pilgrim) }}" class="btn btn-outline-primary btn-xs">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif

@if ($flightStats)
    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-3">Flights by Airline</h6>
                    <canvas id="airlineChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-3">Upcoming Departures</h6>
                    @forelse ($flightStats['upcoming'] as $flight)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $flight->departureCity?->name }} → {{ $flight->arrivalCity?->name }}</span>
                            <span class="text-muted small">{{ $flight->departure_date?->format('d M Y') }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No upcoming flights.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    const colors = ['#b08d57', '#2c3e50', '#5c7cfa', '#40c057', '#f59f00', '#e64980'];

    function makeChart(id, type, dataset, extraOptions = {}) {
        const canvas = document.getElementById(id);
        if (!canvas || !dataset.length) return;

        new Chart(canvas, {
            type,
            data: {
                labels: dataset.map(d => d.label),
                datasets: [{
                    data: dataset.map(d => d.total),
                    backgroundColor: type === 'line' ? colors[0] : colors,
                    borderColor: colors[0],
                    fill: type === 'line',
                }],
            },
            options: { plugins: { legend: { display: type !== 'bar' && type !== 'line' } }, ...extraOptions },
        });
    }

    makeChart('trendChart', 'line', @json($pilgrimStats['monthly_trend'] ?? []));
    makeChart('genderChart', 'doughnut', @json($pilgrimStats['by_gender'] ?? []));
    makeChart('packageChart', 'bar', @json($pilgrimStats['by_package'] ?? []));
    makeChart('airlineChart', 'bar', @json($flightStats['by_airline'] ?? []));
</script>
@endpush