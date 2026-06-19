@php $assigned = isset($role) ? $role->permissions->pluck('id')->toArray() : []; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Permissions</label>
    <div class="col-lg-8">
        <div class="row">
            @foreach ($permissions as $permission)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]"
                               value="{{ $permission->id }}" id="perm-{{ $permission->id }}"
                               {{ in_array($permission->id, $assigned) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>