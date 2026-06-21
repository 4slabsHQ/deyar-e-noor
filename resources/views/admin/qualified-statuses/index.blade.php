@extends('layouts.app')

@section('title', 'Qualified Statuses')
@section('page-title', 'Qualified Statuses')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Qualified Statuses</h4>
        @can('qualified-statuses.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qsModal"
                    onclick="openCreateQsModal()">
                <i class="fas fa-plus me-1"></i> New Qualified Status
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Qualified Statuses</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No qualified statuses yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Name</th>
                            <th>Color</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($qualifiedStatuses as $qs)
                            <tr>
                                <td>{{ $qs->sort_order }}</td>
                                <td class="fw-medium">{{ $qs->name }}</td>
                                <td>
                                    @if ($qs->color)
                                        <span class="badge" style="background-color: {{ $qs->color }};">&nbsp;&nbsp;&nbsp;</span>
                                        {{ $qs->color }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge light badge-{{ $qs->is_active ? 'success' : 'secondary' }}">
                                        {{ $qs->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('qualified-statuses.update')
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                    data-bs-toggle="modal" data-bs-target="#qsModal"
                                                    data-id="{{ $qs->id }}"
                                                    data-name="{{ $qs->name }}"
                                                    data-color="{{ $qs->color }}"
                                                    data-sort-order="{{ $qs->sort_order }}"
                                                    data-is-active="{{ $qs->is_active ? '1' : '0' }}"
                                                    data-update-url="{{ route('admin.qualified-statuses.update', $qs) }}"
                                                    onclick="openEditQsModal(this)">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        @endcan
                                        @can('qualified-statuses.delete')
                                            <form action="{{ route('admin.qualified-statuses.destroy', $qs) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $qs->name }}?')">
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

    <div class="modal fade" id="qsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="qsForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="qsMethod" value="POST">
                    <input type="hidden" name="record_id" id="record_id" value="{{ old('record_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="qsModalTitle">Create Qualified Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Hot, Warm, Cold" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        function openCreateQsModal() {
            document.getElementById('qsForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('qsModalTitle').innerText = 'Create Qualified Status';
            document.getElementById('qsMethod').value = 'POST';
            document.getElementById('qsForm').action = '{{ route('admin.qualified-statuses.store') }}';
            document.getElementById('is_active').checked = true;
        }

        function openEditQsModal(btn) {
            document.getElementById('qsModalTitle').innerText = 'Edit Qualified Status';
            document.getElementById('qsMethod').value = 'PUT';
            document.getElementById('qsForm').action = btn.dataset.updateUrl;
            document.getElementById('record_id').value = btn.dataset.id;
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('color').value = btn.dataset.color || '#6c757d';
            document.getElementById('sort_order').value = btn.dataset.sortOrder;
            document.getElementById('is_active').checked = btn.dataset.isActive === '1';
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const recordId = document.getElementById('record_id').value;
                document.getElementById('qsModalTitle').innerText = recordId ? 'Edit Qualified Status' : 'Create Qualified Status';
                document.getElementById('qsMethod').value = recordId ? 'PUT' : 'POST';
                document.getElementById('qsForm').action = recordId
                    ? '{{ url('admin/qualified-statuses') }}/' + recordId
                    : '{{ route('admin.qualified-statuses.store') }}';
                new bootstrap.Modal(document.getElementById('qsModal')).show();
            });
        @endif
    </script>
    @endpush
@endsection