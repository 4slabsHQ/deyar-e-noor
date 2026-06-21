<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'activity_type' => ['required', 'in:call,email,whatsapp,meeting,note,follow_up'],
            'subject'       => ['nullable', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'outcome'       => ['nullable', 'string', 'max:255'],
            'due_at'        => ['nullable', 'date'],
            'completed_at'  => ['nullable', 'date'],
        ];
    }
}