<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StudentOnboardingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movil_phone' => ['required', 'string', 'max:15'],
            'birth_date' => ['required', 'date'],
            'actual_address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'actual_state' => ['required', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'curp' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'local_phone' => ['nullable', 'string', 'max:15'],
            'profile_photo_path' => ['nullable', 'string', 'max:255'],
        ];
    }
}
