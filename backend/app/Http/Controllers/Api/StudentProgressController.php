<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentProgressController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $controls = $user->subjectControls()
            ->with('subject:id,subject_name')
            ->get();

        $total = $controls->count();
        $approved = $controls->filter(function ($control) {
            return $control->subject_status === SubjectStatus::Approved;
        })->count();
        $enabled = $controls->filter(function ($control) {
            return $control->subject_status === SubjectStatus::Enabled;
        })->count();
        $disabled = $controls->filter(function ($control) {
            return $control->subject_status === SubjectStatus::Disabled;
        })->count();

        $percentage = $total > 0 ? round(($approved / $total) * 100, 2) : 0;

        $subjects = $controls->map(function ($control) {
            $status = $control->subject_status instanceof SubjectStatus
                ? $control->subject_status->name
                : null;

            $statusValue = $control->subject_status instanceof SubjectStatus
                ? $control->subject_status->value
                : null;

            return [
                'subject_id' => $control->subject_id,
                'subject_name' => $control->subject?->subject_name,
                'status' => $status,
                'status_value' => $statusValue,
                'progress_percent' => $control->subject_status === SubjectStatus::Approved ? 100 : 0,
            ];
        })->values();

        return response()->json([
            'data' => [
                'overall' => [
                    'percentage' => $percentage,
                    'approved_count' => $approved,
                    'enabled_count' => $enabled,
                    'disabled_count' => $disabled,
                    'total_count' => $total,
                ],
                'subjects' => $subjects,
            ],
        ]);
    }
}
