<?php
// app/Http/Requests/UpdateSubServiceRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'name'       => ['required', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:255'],
            'is_active'  => ['boolean'],
        ];
    }
}