@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Role</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Role Name <span class="text-danger">*</span></label>
                <div class="col-lg-8">
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-lg-3"></div>
                <div class="col-lg-8">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                        <label class="form-check-label fw-semibold" for="selectAllPermissions">
                            Select All Permissions
                        </label>
                    </div>
                </div>
            </div>

            @include('admin.roles._permissions')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Role</button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const selectAll = document.getElementById('selectAllPermissions');
    const permissionCheckboxes = () => document.querySelectorAll('input[type="checkbox"][name="permissions[]"]');

    selectAll.addEventListener('change', function () {
        permissionCheckboxes().forEach(cb => cb.checked = this.checked);
    });

    // Sync "Select All" state based on individual checkboxes
    document.addEventListener('change', function (e) {
        if (e.target.matches('input[type="checkbox"][name="permissions[]"]')) {
            const all = permissionCheckboxes();
            selectAll.checked = [...all].every(cb => cb.checked);
            selectAll.indeterminate = !selectAll.checked && [...all].some(cb => cb.checked);
        }
    });

    // Set initial state on page load
    window.addEventListener('DOMContentLoaded', () => {
        const all = permissionCheckboxes();
        selectAll.checked = all.length > 0 && [...all].every(cb => cb.checked);
        selectAll.indeterminate = !selectAll.checked && [...all].some(cb => cb.checked);
    });
</script>
@endpush