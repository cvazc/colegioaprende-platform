<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamAttemptCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attempt_type' => ['required', 'string', Rule::in(['practice', 'midterm', 'final'])],
            'question_count' => ['sometimes', 'integer', 'min:5', 'max:100'],
            'topic' => ['sometimes', 'string', 'max:100'],
        ];
    }
}
