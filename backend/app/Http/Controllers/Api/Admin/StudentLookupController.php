<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentLookupController extends Controller
{
    public function byEmail(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $student = Student::query()
            ->where('email', $validated['email'])
            ->with(['subjectControls.subject'])
            ->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'email' => $student->email,
                    'first_name' => $student->first_name,
                    'surnames' => $student->surnames,
                ],
                'subjects' => $student->subjectControls->map(function ($control) {
                    return [
                        'subject_id' => $control->subject_id,
                        'subject_name' => $control->subject?->subject_name,
                        'status' => $control->subject_status?->name,
                        'status_value' => $control->subject_status?->value,
                        'score' => $control->score,
                        'exam_id' => $control->exam_id,
                    ];
                })->values(),
            ],
        ]);
    }
}
