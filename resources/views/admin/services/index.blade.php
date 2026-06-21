@extends('layouts.app')

@section('title', 'Services')
@section('page-title', 'Services')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Services</h4>
        @can('services.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal"
                    onclick="openCreateServiceModal()">
                <i class="fas fa-plus me-1"></i> New Service
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Services</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No services yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td class="fw-medium">{{ $service->name }}</td>
                                <td>{{ $service->code ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $service->is_active ? 'success' : 'secondary' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('services.update')
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                    data-bs-toggle="modal" data-bs-target="#serviceModal"
                                                    data-id="{{ $service->id }}"
                                                    data-name="{{ $service->name }}"
                                                    data-code="{{ $service->code }}"
                                                    data-is-active="{{ $service->is_active ? '1' : '0' }}"
                                                    data-update-url="{{ route('admin.services.update', $service) }}"
                                                    onclick="openEditServiceModal(this)">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        @endcan
                                        @can('services.delete')
                                            <form action="{{ route('admin.services.destroy', $service) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $service->name }}?')">
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

    <div class="modal fade" id="serviceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="serviceForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="serviceMethod" value="POST">
                    <input type="hidden" name="record_id" id="record_id" value="{{ old('record_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="serviceModalTitle">Create Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Visa, Umrah, Tour" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}"
                                   class="form-control @error('code') is-invalid @enderror">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        function openCreateServiceModal() {
            document.getElementById('serviceForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('serviceModalTitle').innerText = 'Create Service';
            document.getElementById('serviceMethod').value = 'POST';
            document.getElementById('serviceForm').action = '{{ route('admin.services.store') }}';
            document.getElementById('is_active').checked = true;
        }

        function openEditServiceModal(btn) {
            document.getElementById('serviceModalTitle').innerText = 'Edit Service';
            document.getElementById('serviceMethod').value = 'PUT';
            document.getElementById('serviceForm').action = btn.dataset.updateUrl;
            document.getElementById('record_id').value = btn.dataset.id;
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('code').value = btn.dataset.code ?? '';
            document.getElementById('is_active').checked = btn.dataset.isActive === '1';
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const recordId = document.getElementById('record_id').value;
                document.getElementById('serviceModalTitle').innerText = recordId ? 'Edit Service' : 'Create Service';
                document.getElementById('serviceMethod').value = recordId ? 'PUT' : 'POST';
                document.getElementById('serviceForm').action = recordId
                    ? '{{ url('admin/services') }}/' + recordId
                    : '{{ route('admin.services.store') }}';
                new bootstrap.Modal(document.getElementById('serviceModal')).show();
            });
        @endif
    </script>
    @endpush
@endsection