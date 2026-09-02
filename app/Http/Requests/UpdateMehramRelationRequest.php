<?php

namespace App\Http\Requests;

use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMehramRelationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', SeasonValidation::unique('mehram_relations', 'name', $this->route('mehram_relation'))],
            'is_active' => ['boolean'],
        ];
    }
}
