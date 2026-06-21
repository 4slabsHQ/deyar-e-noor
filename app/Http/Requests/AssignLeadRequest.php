<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'assigned_to' => ['required', 'exists:users,id'],
        ];
    }
}