<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'full_name'            => ['required', 'string', 'max:255'],
            'email'                => ['nullable', 'email', 'max:255'],
            'phone'                => ['required', 'string', 'max:255'],
            'whatsapp'             => ['nullable', 'string', 'max:255'],
            'country_id'           => ['nullable', 'exists:countries,id'],
            'city_id'              => ['nullable', 'exists:cities,id'],
            'branch_id'            => ['nullable', 'exists:branches,id'],
            'service_id'           => ['nullable', 'exists:services,id'],
            'sub_service_id'       => ['nullable', 'exists:sub_services,id'],
            'channel_id'           => ['nullable', 'exists:channels,id'],
            'campaign_id'          => ['nullable', 'exists:campaigns,id'],
            'lead_status_id'       => ['nullable', 'exists:lead_statuses,id'],
            'qualified_status_id'  => ['nullable', 'exists:qualified_statuses,id'],
            'assigned_to'          => ['nullable', 'exists:users,id'],
            'priority'             => ['required', 'in:low,medium,high'],
            'expected_value'       => ['nullable', 'numeric', 'min:0'],
            'expected_close_date'  => ['nullable', 'date'],
            'next_follow_up_at'    => ['nullable', 'date'],
            'notes'                => ['nullable', 'string'],
            'is_active'            => ['boolean'],
        ];
    }
}