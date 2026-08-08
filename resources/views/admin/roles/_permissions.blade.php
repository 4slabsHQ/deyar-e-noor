@php
    $assigned = isset($role) ? $role->permissions->pluck('id')->toArray() : [];
    $selected = old('permissions', $assigned);
    $showSelectAll = $showSelectAll ?? false;
@endphp

<x-admin.form-section title="Permissions" data-role-permissions>
    @if ($showSelectAll)
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllPermissions" data-role-permissions-select-all>
                <label class="form-check-label fw-semibold" for="selectAllPermissions">
                    Select All Permissions
                </label>
            </div>
        </div>
    @endif

    <div class="row g-2">
        @foreach ($permissions as $permission)
            <div class="col-lg-4 col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]"
                           value="{{ $permission->id }}" id="perm-{{ $permission->id }}"
                           @checked(in_array($permission->id, $selected))>
                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                        {{ $permission->name }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</x-admin.form-section>
