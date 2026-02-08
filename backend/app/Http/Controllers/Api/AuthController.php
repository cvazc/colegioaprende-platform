<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Student;
use App\Rules\RecaptchaToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!$request->has('recaptcha_token') && $request->has('g-recaptcha-response')) {
            $request->merge(['recaptcha_token' => $request->input('g-recaptcha-response')]);
        }

        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];

        if (config('services.recaptcha.enabled')) {
            $rules['recaptcha_token'] = ['required', 'string', new RecaptchaToken()];
        }

        $validated = $request->validate($rules);

        $employee = Employee::query()->where('email', $validated['email'])->first();
        if ($employee && Hash::check($validated['password'], $employee->password)) {
            $token = $employee->createToken('api')->plainTextToken;

            return response()->json([
                'token' => $token,
                'role' => 'employee',
                'user' => [
                    'id' => $employee->id,
                    'email' => $employee->email,
                    'first_name' => $employee->first_name,
                    'surnames' => $employee->surnames,
                    'employee_type' => $employee->employee_type,
                    'role_label' => $employee->roleLabel(),
                    'permissions' => $employee->permissions(),
                ],
            ]);
        }

        $student = Student::query()->where('email', $validated['email'])->first();
        if ($student && Hash::check($validated['password'], $student->password)) {
            $token = $student->createToken('api')->plainTextToken;

            return response()->json([
                'token' => $token,
                'role' => 'student',
                'user' => [
                    'id' => $student->id,
                    'email' => $student->email,
                    'first_name' => $student->first_name,
                    'surnames' => $student->surnames,
                    'status' => $student->status?->value,
                    'profile_completed_at' => $student->profile_completed_at,
                ],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        $role = match (true) {
            $user instanceof \App\Models\Employee => 'employee',
            $user instanceof \App\Models\Student => 'student',
            default => 'unknown',
        };

        return response()->json([
            'data' => [
                'role' => $role,
                'user' => match (true) {
                    $user instanceof Employee => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'first_name' => $user->first_name,
                        'surnames' => $user->surnames,
                        'employee_type' => $user->employee_type,
                        'role_label' => $user->roleLabel(),
                        'permissions' => $user->permissions(),
                    ],
                    $user instanceof Student => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'first_name' => $user->first_name,
                        'surnames' => $user->surnames,
                        'status' => $user->status?->value,
                        'profile_completed_at' => $user->profile_completed_at,
                    ],
                    default => $user,
                },
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user?->currentAccessToken()?->delete();

        return response()->json([
            'data' => [
                'message' => 'Logged out successfully',
            ]
        ]);
    }
}
