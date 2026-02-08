<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCohortStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'course_type' => ['nullable', 'string', 'max:100'],
            'enrollment_type' => ['nullable', 'string', 'max:255'],
            'calendar_template_id' => ['nullable', 'integer', 'exists:calendar_templates,id'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'auto_assign' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'rules' => ['sometimes', 'array'],
            'rules.*.field' => ['required_with:rules', 'string', 'max:50'],
            'rules.*.operator' => ['required_with:rules', 'string', 'in:equals,not_equals,in,not_in'],
            'rules.*.value' => ['required_with:rules', 'string', 'max:255'],
            'rules.*.is_active' => ['sometimes', 'boolean'],
        ];
    }
}
