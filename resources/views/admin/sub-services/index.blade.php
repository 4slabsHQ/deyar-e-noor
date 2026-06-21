@extends('layouts.app')

@section('title', 'Sub Services')
@section('page-title', 'Sub Services')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Sub Services</h4>
        @can('sub-services.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subServiceModal"
                    onclick="openCreateSubServiceModal()">
                <i class="fas fa-plus me-1"></i> New Sub Service
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Sub Services</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No sub-services yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Service</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subServices as $subService)
                            <tr>
                                <td class="fw-medium">{{ $subService->name }}</td>
                                <td>{{ $subService->service->name ?? '—' }}</td>
                                <td>{{ $subService->code ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $subService->is_active ? 'success' : 'secondary' }}">
                                        {{ $subService->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('sub-services.update')
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                    data-bs-toggle="modal" data-bs-target="#subServiceModal"
                                                    data-id="{{ $subService->id }}"
                                                    data-name="{{ $subService->name }}"
                                                    data-code="{{ $subService->code }}"
                                                    data-service-id="{{ $subService->service_id }}"
                                                    data-is-active="{{ $subService->is_active ? '1' : '0' }}"
                                                    data-update-url="{{ route('admin.sub-services.update', $subService) }}"
                                                    onclick="openEditSubServiceModal(this)">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        @endcan
                                        @can('sub-services.delete')
                                            <form action="{{ route('admin.sub-services.destroy', $subService) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $subService->name }}?')">
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

    <div class="modal fade" id="subServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="subServiceForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="subServiceMethod" value="POST">
                    <input type="hidden" name="record_id" id="record_id" value="{{ old('record_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="subServiceModalTitle">Create Sub Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Service <span class="text-danger">*</span></label>
                            <select name="service_id" id="service_id" class="form-control @error('service_id') is-invalid @enderror" required>
                                <option value="">-- Select Service --</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" {{ (string) old('service_id') === (string) $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
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
        function openCreateSubServiceModal() {
            document.getElementById('subServiceForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('subServiceModalTitle').innerText = 'Create Sub Service';
            document.getElementById('subServiceMethod').value = 'POST';
            document.getElementById('subServiceForm').action = '{{ route('admin.sub-services.store') }}';
            document.getElementById('is_active').checked = true;
        }

        function openEditSubServiceModal(btn) {
            document.getElementById('subServiceModalTitle').innerText = 'Edit Sub Service';
            document.getElementById('subServiceMethod').value = 'PUT';
            document.getElementById('subServiceForm').action = btn.dataset.updateUrl;
            document.getElementById('record_id').value = btn.dataset.id;
            document.getElementById('service_id').value = btn.dataset.serviceId ?? '';
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('code').value = btn.dataset.code ?? '';
            document.getElementById('is_active').checked = btn.dataset.isActive === '1';
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const recordId = document.getElementById('record_id').value;
                document.getElementById('subServiceModalTitle').innerText = recordId ? 'Edit Sub Service' : 'Create Sub Service';
                document.getElementById('subServiceMethod').value = recordId ? 'PUT' : 'POST';
                document.getElementById('subServiceForm').action = recordId
                    ? '{{ url('admin/sub-services') }}/' + recordId
                    : '{{ route('admin.sub-services.store') }}';
                new bootstrap.Modal(document.getElementById('subServiceModal')).show();
            });
        @endif
    </script>
    @endpush
@endsection