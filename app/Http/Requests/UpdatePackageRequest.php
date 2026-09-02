<?php

namespace App\Http\Requests;

use App\Enums\PackageDuration;
use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:50', SeasonValidation::unique('packages', 'number', $this->route('package'))],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'days' => ['required', 'integer', 'min:0'],
            'qurbani_included' => ['boolean'],
            'duration' => ['required', Rule::enum(PackageDuration::class)],
            'limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('limit') && $this->input('limit') === '') {
            $this->merge(['limit' => null]);
        }

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'qurbani_included' => $this->boolean('qurbani_included'),
        ]);
    }
}
