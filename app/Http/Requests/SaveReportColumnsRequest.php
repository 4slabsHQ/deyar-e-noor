<?php

namespace App\Http\Requests;

use App\Reports\ReportRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use InvalidArgumentException;

class SaveReportColumnsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reports.view') ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'columns' => ['required', 'array', 'min:1'],
            'columns.*' => ['string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            try {
                $this->validatedColumns();
            } catch (InvalidArgumentException $exception) {
                $validator->errors()->add('columns', $exception->getMessage());
            }
        });
    }

    public function reportKey(): string
    {
        return (string) $this->route('report');
    }

    /** @return list<string> */
    public function validatedColumns(): array
    {
        $definition = app(ReportRegistry::class)->get($this->reportKey());
        $columns = $this->input('columns');

        if (! is_array($columns)) {
            return [];
        }

        return $definition->validateColumns($columns);
    }
}
