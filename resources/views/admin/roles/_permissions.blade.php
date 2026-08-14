@php
    $assigned = isset($role) ? $role->permissions->pluck('id')->toArray() : [];
    $selected = old('permissions', $assigned);
    $showSelectAll = $showSelectAll ?? true;
    $groupedPermissions = \App\Support\PermissionCatalog::groupedPermissions();
    $permissionsByName = $permissions->keyBy('name');
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

    @foreach ($groupedPermissions as $groupLabel => $permissionNames)
        @php $groupKey = \Illuminate\Support\Str::slug($groupLabel); @endphp
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="text-uppercase text-muted mb-0">{{ $groupLabel }}</h6>
                <button type="button"
                        class="btn btn-link btn-sm p-0 text-decoration-none"
                        data-permission-group-select="{{ $groupKey }}">
                    Select group
                </button>
            </div>
            <div class="row g-2">
                @foreach ($permissionNames as $permissionName)
                    @php $permission = $permissionsByName->get($permissionName); @endphp
                    @if ($permission)
                        <div class="col-lg-4 col-md-6">
                            <div class="form-check">
                                <input class="form-check-input js-permission-checkbox" type="checkbox" name="permissions[]"
                                       value="{{ $permission->id }}" id="perm-{{ $permission->id }}"
                                       data-permission-group="{{ $groupKey }}"
                                       @checked(in_array($permission->id, $selected))>
                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</x-admin.form-section>
