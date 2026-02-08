<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminEmployeeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:45'],
            'surnames' => ['required', 'string', 'max:45'],
            'email' => ['required', 'email', 'max:100', Rule::unique('employee', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'employee_type' => ['required', 'integer', Rule::in([1, 2])],
        ];
    }
}
