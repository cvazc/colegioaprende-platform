<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentSubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json([
                'message' => 'Forbidden.',
            ], 403);
        }

        $rows = $user->subjectControls()
            ->with('subject:id,subject_name,pdf_file,enrollment_type')
            ->get()
            ->map(function ($control) {
                return [
                    'subject_id' => $control->subject_id,
                    'subject_name' => $control->subject?->subject_name,
                    'enrollment_type' => $control->subject?->enrollment_type,
                    'pdf_file' => $control->subject?->pdf_file,
                    'status' => $control->subject_status instanceof SubjectStatus
                        ? $control->subject_status->name
                        : null,
                    'status_value' => $control->subject_status instanceof SubjectStatus
                        ? $control->subject_status->value
                        : null,
                    'score' => $control->score,
                    'exam_id' => $control->exam_id,
                ];
            });

        return response()->json([
            'data' => $rows,
        ]);
    }
}
