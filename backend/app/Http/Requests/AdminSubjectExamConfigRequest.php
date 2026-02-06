<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSubjectExamConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'practice_questions' => ['sometimes', 'integer', 'min:5', 'max:100'],
            'midterm_questions' => ['sometimes', 'integer', 'min:5', 'max:100'],
            'final_questions' => ['sometimes', 'integer', 'min:5', 'max:100'],
            'allow_open_practice' => ['sometimes', 'boolean'],
        ];
    }
}
