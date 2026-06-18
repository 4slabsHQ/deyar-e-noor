<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Super Admin') || $this->user()->can('roles.update');
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name,' . $this->route('role')->id],
            'permissions'   => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }
}