@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
@if ($pilgrimStats)
    <div class="row g-3 mb-3">
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
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <span class="deyar-metric__label">Signed in as</span>
                    <span class="deyar-metric__value deyar-metric__value--text">{{ auth()->user()->name }}</span>
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
    </div>

    @if ($pilgrimStats['recent']->isNotEmpty())
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Recent Registrations</h6>
                <div class="table-responsive">
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
        </div>
    @endif
@else
    <div class="row g-3">
        <div class="col-xl-4 col-sm-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="deyar-metric__label">Signed in as</span>
                    <span class="deyar-metric__value deyar-metric__value--text">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
