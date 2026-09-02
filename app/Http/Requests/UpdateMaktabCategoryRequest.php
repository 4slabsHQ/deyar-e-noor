<?php

namespace App\Http\Requests;

use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaktabCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $id = $this->route('maktab_category')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('maktab_categories', 'name')
                    ->where(fn ($query) => $query
                        ->where('hajj_year', SeasonValidation::activeYear())
                        ->where('zone', $this->input('zone')))
                    ->ignore($id),
            ],
            'zone' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }
}
