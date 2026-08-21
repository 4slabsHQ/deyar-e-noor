@extends('layouts.app')

@section('title', 'Hajj Seasons')
@section('page-title', 'Hajj Seasons')

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card admin-index-card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Hajj Seasons</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Only one season can be active at a time. The dashboard uses the active season for registration counts, quota utilisation, and recent registrations.
                    </p>

                    <table data-datatable data-empty-message="No Hajj seasons yet." class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Activated</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($seasons as $season)
                                <tr>
                                    <td class="fw-medium">Hajj {{ $season->year }}</td>
                                    <td>
                                        @if ($season->isActive())
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Archived</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $season->activated_at?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td>
                                        @can('hajj-seasons.manage')
                                            <div class="admin-table-actions d-flex align-items-center gap-1">
                                                @if ($season->isActive())
                                                    <span class="text-muted">Current</span>
                                                @else
                                                    <form method="POST" action="{{ route('admin.hajj-seasons.activate', $season) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary btn-xs">
                                                            Set active
                                                        </button>
                                                    </form>
                                                    <form method="POST"
                                                          action="{{ route('admin.hajj-seasons.destroy', $season) }}"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Remove Hajj {{ $season->year }} from the seasons list? Registration data will not be deleted.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-xs">
                                                            Remove
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @can('hajj-seasons.manage')
            <div class="col-xl-4">
                <div class="card admin-index-card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Add Season</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.hajj-seasons.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="year">Hajj Year</label>
                                <input type="number" name="year" id="year" min="2000" max="2100"
                                       value="{{ old('year') }}"
                                       class="form-control @error('year') is-invalid @enderror"
                                       placeholder="e.g. 2028">
                                @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Add season</button>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection
