<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkFlightAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('flights.assign') ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['assign', 'remove'])],
            'select_all' => ['sometimes', 'boolean'],
            'pilgrim_ids' => ['required_without:select_all', 'array'],
            'pilgrim_ids.*' => ['integer', 'exists:pilgrims,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'pod_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'form_owner_id' => ['nullable', 'integer', 'exists:form_owners,id'],
            'family_code' => ['nullable', 'string', 'max:50'],
            'search' => ['nullable', 'string', 'max:100'],
            'assignment_status' => ['nullable', Rule::in(['all', 'on_flight', 'not_on_flight'])],
        ];
    }

    /** @return list<int> */
    public function pilgrimIds(): array
    {
        if ($this->boolean('select_all')) {
            return [];
        }

        return array_values(array_map('intval', $this->input('pilgrim_ids', [])));
    }

    public function shouldSelectAll(): bool
    {
        return $this->boolean('select_all');
    }

    /** @return array<string, mixed> */
    public function filters(): array
    {
        return [
            'company_id' => $this->input('company_id'),
            'pod_city_id' => $this->input('pod_city_id'),
            'package_id' => $this->input('package_id'),
            'form_owner_id' => $this->input('form_owner_id'),
            'family_code' => $this->input('family_code'),
            'search' => $this->input('search'),
            'assignment_status' => $this->input('assignment_status', 'all'),
        ];
    }
}
