<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeLeadStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'lead_status_id' => ['required', 'exists:lead_statuses,id'],
            'reason'         => ['nullable', 'string', 'max:255'], // used as lost_reason when status is "lost"
        ];
    }
}