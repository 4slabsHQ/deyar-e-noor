<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Reports\ReportRegistry;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as ValidatorInstance;
use InvalidArgumentException;

class RunReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->routeIs('admin.reports.export.*')) {
            return $this->user()?->can('reports.export') ?? false;
        }

        return $this->user()?->can('reports.view') ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $registry = app(ReportRegistry::class);

        $rules = [
            'report' => ['nullable', Rule::in(array_keys($registry->options()))],
            'run' => ['sometimes', 'boolean'],
            'columns.*' => ['string'],
            'hajj_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'maktab_category_id' => ['nullable', 'integer', 'exists:maktab_categories,id'],
            'form_owner_id' => ['nullable', 'integer', 'exists:form_owners,id'],
            'pod_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'care_off_id' => ['nullable', 'integer', 'exists:care_offs,id'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'entry_from' => ['nullable', 'date'],
            'entry_to' => ['nullable', 'date', 'after_or_equal:entry_from'],
            'search' => ['nullable', 'string', 'max:100'],
        ];

        if ($this->shouldRun()) {
            $rules['columns'] = ['required', 'array', 'min:1'];
        }

        return $rules;
    }

    public function withValidator(ValidatorInstance $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (! $this->shouldRun() && ! $this->routeIs('admin.reports.export.*')) {
                return;
            }

            try {
                $this->selectedColumns();
            } catch (InvalidArgumentException $exception) {
                $validator->errors()->add('columns', $exception->getMessage());
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->routeIs('admin.reports.results') || $this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'message' => $validator->errors()->first() ?: 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422));
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    public function reportKey(): string
    {
        $routeReport = $this->route('report');

        if (is_string($routeReport) && $routeReport !== '') {
            return $routeReport;
        }

        return $this->input('report', app(ReportRegistry::class)->defaultKey());
    }

    public function shouldRun(): bool
    {
        return $this->boolean('run')
            || $this->routeIs('admin.reports.export.*')
            || $this->routeIs('admin.reports.results');
    }

    /** @return list<string> */
    public function selectedColumns(): array
    {
        $registry = app(ReportRegistry::class);
        $definition = $registry->get($this->reportKey());
        $columns = $this->input('columns');

        if (! is_array($columns) || $columns === []) {
            return $definition->defaultColumns();
        }

        return $definition->validateColumns($columns);
    }

    /** @return array<string, mixed> */
    public function filters(): array
    {
        return $this->safe()->only([
            'hajj_year',
            'company_id',
            'package_id',
            'maktab_category_id',
            'form_owner_id',
            'pod_city_id',
            'care_off_id',
            'gender',
            'entry_from',
            'entry_to',
            'search',
        ]);
    }
}
