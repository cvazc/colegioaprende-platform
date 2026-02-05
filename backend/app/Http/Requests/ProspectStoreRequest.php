<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProspectStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $courseTypes = [
            'Preparatoria 4 meses 16 sesiones',
            'Secundaria 4 meses 16 sesiones',
            'Idioma Francés',
            'Licenciatura en Administración',
        ];

        $enrollmentTypes = [
            'Estudiante General',
            'Estudiante Preferencial',
            'Estudiante Exclusivo',
        ];

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'surnames' => ['required', 'string', 'max:255'],
            'actual_state' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('prospect', 'email'),
                Rule::unique('student', 'email'),
            ],
            'course_type' => ['required', 'string', Rule::in($courseTypes)],
            'enrollment_type' => ['required', 'string', Rule::in($enrollmentTypes)],
            'local_phone' => ['nullable', 'string', 'max:15'],
            'movil_phone' => ['nullable', 'string', 'max:15'],
            'birth_date' => ['nullable', 'date'],
            'actual_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'curp' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'deposit_date' => ['nullable', 'string', 'max:255'],
        ];
    }
}
