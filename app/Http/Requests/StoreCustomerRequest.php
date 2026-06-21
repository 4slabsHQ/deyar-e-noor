<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'code'             => ['nullable', 'string', 'max:255', Rule::unique('customers', 'code')],
            'email'            => ['nullable', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:255'],
            'whatsapp'         => ['nullable', 'string', 'max:255'],
            'passport_number'  => ['nullable', 'string', 'max:255'],
            'cnic'             => ['nullable', 'string', 'max:255'],
            'dob'              => ['nullable', 'date'],
            'gender'           => ['nullable', 'in:male,female,other'],
            'address'          => ['nullable', 'string'],
            'country_id'       => ['nullable', 'exists:countries,id'],
            'city_id'          => ['nullable', 'exists:cities,id'],
            'nationality'      => ['nullable', 'string', 'max:255'],
            'customer_type'    => ['required', 'in:individual,corporate,walk_in'],
            'company_name'     => ['nullable', 'string', 'max:255'],
            'tax_number'       => ['nullable', 'string', 'max:255'],
            'credit_limit'     => ['nullable', 'numeric', 'min:0'],
            'credit_days'      => ['nullable', 'integer', 'min:0'],
            'is_active'        => ['boolean'],
            'notes'            => ['nullable', 'string'],
        ];
    }
}