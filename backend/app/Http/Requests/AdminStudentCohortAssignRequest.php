<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStudentCohortAssignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cohort_id' => ['required', 'integer', 'exists:cohorts,id'],
            'reason' => ['nullable', 'string', 'max:255'],
            'force' => ['sometimes', 'boolean'],
        ];
    }
}
