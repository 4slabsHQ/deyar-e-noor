@extends('layouts.app')

@section('title', 'Lead Statuses')
@section('page-title', 'Lead Statuses')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Lead Statuses</h4>
        @can('lead-statuses.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal"
                    onclick="openCreateStatusModal()">
                <i class="fas fa-plus me-1"></i> New Status
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Lead Statuses</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No lead statuses yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Name</th>
                            <th>Color</th>
                            <th>Won</th>
                            <th>Lost</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leadStatuses as $status)
                            <tr>
                                <td>{{ $status->sort_order }}</td>
                                <td class="fw-medium">{{ $status->name }}</td>
                                <td>
                                    @if ($status->color)
                                        <span class="badge" style="background-color: {{ $status->color }};">&nbsp;&nbsp;&nbsp;</span>
                                        {{ $status->color }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{!! $status->is_won ? '<i class="fas fa-check text-success"></i>' : '—' !!}</td>
                                <td>{!! $status->is_lost ? '<i class="fas fa-check text-danger"></i>' : '—' !!}</td>
                                <td>
                                    <span class="badge light badge-{{ $status->is_active ? 'success' : 'secondary' }}">
                                        {{ $status->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('lead-statuses.update')
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                    data-bs-toggle="modal" data-bs-target="#statusModal"
                                                    data-id="{{ $status->id }}"
                                                    data-name="{{ $status->name }}"
                                                    data-slug="{{ $status->slug }}"
                                                    data-color="{{ $status->color }}"
                                                    data-sort-order="{{ $status->sort_order }}"
                                                    data-is-won="{{ $status->is_won ? '1' : '0' }}"
                                                    data-is-lost="{{ $status->is_lost ? '1' : '0' }}"
                                                    data-is-active="{{ $status->is_active ? '1' : '0' }}"
                                                    data-update-url="{{ route('admin.lead-statuses.update', $status) }}"
                                                    onclick="openEditStatusModal(this)">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        @endcan
                                        @can('lead-statuses.delete')
                                            <form action="{{ route('admin.lead-statuses.destroy', $status) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $status->name }}?')">
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

    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="statusForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="statusMethod" value="POST">
                    <input type="hidden" name="record_id" id="record_id" value="{{ old('record_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="statusModalTitle">Create Lead Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. New, Contacted, Won" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                   class="form-control @error('slug') is-invalid @enderror" required>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Auto-filled from name. Used internally, e.g. "won".</small>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Color</label>
                                <input type="color" name="color" id="color" value="{{ old('color', '#6c757d') }}"
                                       class="form-control form-control-color @error('color') is-invalid @enderror">
                                @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" min="0" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                       class="form-control @error('sort_order') is-invalid @enderror">
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="is_won" value="0">
                            <input class="form-check-input" type="checkbox" name="is_won" value="1" id="is_won"
                                   {{ old('is_won') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_won">This status means the lead was Won</label>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="is_lost" value="0">
                            <input class="form-check-input" type="checkbox" name="is_lost" value="1" id="is_lost"
                                   {{ old('is_lost') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_lost">This status means the lead was Lost</label>
                        </div>

                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let slugTouched = false;

        document.getElementById('slug').addEventListener('input', () => { slugTouched = true; });

        document.getElementById('name').addEventListener('input', function () {
            if (slugTouched) return;
            document.getElementById('slug').value = this.value
                .toLowerCase().trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        });

        function openCreateStatusModal() {
            document.getElementById('statusForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('statusModalTitle').innerText = 'Create Lead Status';
            document.getElementById('statusMethod').value = 'POST';
            document.getElementById('statusForm').action = '{{ route('admin.lead-statuses.store') }}';
            document.getElementById('is_active').checked = true;
            document.getElementById('is_won').checked = false;
            document.getElementById('is_lost').checked = false;
            slugTouched = false;
        }

        function openEditStatusModal(btn) {
            document.getElementById('statusModalTitle').innerText = 'Edit Lead Status';
            document.getElementById('statusMethod').value = 'PUT';
            document.getElementById('statusForm').action = btn.dataset.updateUrl;
            document.getElementById('record_id').value = btn.dataset.id;
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('slug').value = btn.dataset.slug;
            document.getElementById('color').value = btn.dataset.color || '#6c757d';
            document.getElementById('sort_order').value = btn.dataset.sortOrder;
            document.getElementById('is_won').checked = btn.dataset.isWon === '1';
            document.getElementById('is_lost').checked = btn.dataset.isLost === '1';
            document.getElementById('is_active').checked = btn.dataset.isActive === '1';
            slugTouched = true; // don't let typing the name overwrite an existing slug
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const recordId = document.getElementById('record_id').value;
                document.getElementById('statusModalTitle').innerText = recordId ? 'Edit Lead Status' : 'Create Lead Status';
                document.getElementById('statusMethod').value = recordId ? 'PUT' : 'POST';
                document.getElementById('statusForm').action = recordId
                    ? '{{ url('admin/lead-statuses') }}/' + recordId
                    : '{{ route('admin.lead-statuses.store') }}';
                slugTouched = true;
                new bootstrap.Modal(document.getElementById('statusModal')).show();
            });
        @endif
    </script>
    @endpush
@endsection