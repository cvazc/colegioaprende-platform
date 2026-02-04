<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\SubjectStatus;
use App\Http\Controllers\Controller;
use App\Models\ControlSubjectStudent;
use App\Models\Employee;
use Illuminate\Http\Request;

class StudentSubjectStatusController extends Controller
{
    public function update(Request $request, int $studentId, int $subjectId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'subject_status' => ['required', 'integer', 'in:0,1,2'],
            'score' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ]);

        $control = ControlSubjectStudent::query()
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$control) {
            return response()->json(['message' => 'Student subject record not found.'], 404);
        }

        $control->subject_status = SubjectStatus::from((int) $validated['subject_status']);

        if (array_key_exists('score', $validated)) {
            $control->score = (int) $validated['score'];
        }

        $control->save();

        return response()->json([
            'data' => [
                'student_id' => $control->student_id,
                'subject_id' => $control->subject_id,
                'status' => $control->subject_status->name,
                'status_value' => $control->subject_status->value,
                'score' => $control->score,
            ],
        ]);
    }
}
