<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminEmployeeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = (int) $this->route('employeeId');

        return [
            'first_name' => ['sometimes', 'string', 'max:45'],
            'surnames' => ['sometimes', 'string', 'max:45'],
            'email' => [
                'sometimes',
                'email',
                'max:100',
                Rule::unique('employee', 'email')->ignore($employeeId, 'id'),
            ],
            'password' => ['sometimes', 'string', 'min:8'],
            'employee_type' => ['sometimes', 'integer', Rule::in([1, 2])],
        ];
    }
}
