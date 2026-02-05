<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminQuestionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['mcq', 'true_false', 'open'])],
            'prompt' => ['required', 'string'],
            'topic' => ['nullable', 'string', 'max:100'],
            'explanation' => ['nullable', 'string'],
            'difficulty' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'options' => ['sometimes', 'array'],
            'options.*.text' => ['required_with:options', 'string'],
            'options.*.is_correct' => ['sometimes', 'boolean'],
            'options.*.label' => ['sometimes', 'string', 'max:10'],
        ];
    }
}
