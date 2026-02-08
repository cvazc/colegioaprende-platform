<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCalendarTemplateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'events' => ['sometimes', 'array'],
            'events.*.event_code' => ['nullable', 'string', 'max:50'],
            'events.*.title' => ['required_with:events', 'string', 'max:200'],
            'events.*.description' => ['nullable', 'string'],
            'events.*.due_date' => ['required_with:events', 'date'],
            'events.*.is_payment' => ['sometimes', 'boolean'],
            'events.*.is_active' => ['sometimes', 'boolean'],
        ];
    }
}
