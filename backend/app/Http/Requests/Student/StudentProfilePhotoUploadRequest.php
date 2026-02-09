<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StudentProfilePhotoUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }
}
