<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStudentCalendarAssignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'calendar_template_id' => ['required', 'integer', 'exists:calendar_templates,id'],
            'assigned_start_date' => ['nullable', 'date'],
        ];
    }
}
