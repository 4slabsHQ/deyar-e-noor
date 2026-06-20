<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'supplier_category_id' => ['nullable', 'exists:supplier_categories,id'],
            'name'                  => ['required', 'string', 'max:255'],
            'code'                  => ['nullable', 'string', 'max:255', Rule::unique('suppliers', 'code')],
            'contact_person'        => ['nullable', 'string', 'max:255'],
            'email'                 => ['nullable', 'email', 'max:255'],
            'phone'                 => ['nullable', 'string', 'max:255'],
            'whatsapp'              => ['nullable', 'string', 'max:255'],
            'address'               => ['nullable', 'string'],
            'country_id'            => ['nullable', 'exists:countries,id'],
            'city_id'               => ['nullable', 'exists:cities,id'],
            'tax_number'            => ['nullable', 'string', 'max:255'],
            'bank_name'             => ['nullable', 'string', 'max:255'],
            'bank_account'          => ['nullable', 'string', 'max:255'],
            'bank_iban'             => ['nullable', 'string', 'max:255'],

            // Portal access — kept optional now, ready for when the supplier portal is built
            'portal_access'         => ['boolean'],
            'portal_email'          => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'portal_email')],
            'portal_password'       => ['nullable', 'string', 'min:8'],

            'is_active'             => ['boolean'],
            'notes'                 => ['nullable', 'string'],
        ];
    }
}