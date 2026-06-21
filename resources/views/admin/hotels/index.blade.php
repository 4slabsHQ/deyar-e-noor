@extends('layouts.app')

@section('title', 'Hotels')
@section('page-title', 'Hotels')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Hotels</h4>
        @can('hotels.create')
            <a href="{{ route('admin.hotels.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Hotel
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Hotels</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No hotels yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Rating</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hotels as $hotel)
                            <tr>
                                <td class="fw-medium">{{ $hotel->name }}</td>
                                <td>{{ $hotel->code ?? '—' }}</td>
                                <td>
                                    @if ($hotel->star_rating)
                                        {{ $hotel->star_rating }} <i class="fas fa-star text-warning"></i>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $hotel->country->name ?? '—' }}</td>
                                <td>{{ $hotel->city->name ?? '—' }}</td>
                                <td>{{ $hotel->phone ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $hotel->is_active ? 'success' : 'secondary' }}">
                                        {{ $hotel->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('hotels.update')
                                            <a href="{{ route('admin.hotels.edit', $hotel) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('hotels.delete')
                                            <form action="{{ route('admin.hotels.destroy', $hotel) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $hotel->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger shadow btn-xs sharp">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection