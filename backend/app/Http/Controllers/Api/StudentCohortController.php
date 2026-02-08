<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CohortMember;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentCohortController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $member = CohortMember::query()
            ->where('student_id', $user->id)
            ->with(['cohort.calendarTemplate:id,name'])
            ->first();

        if (!$member) {
            return response()->json([
                'data' => null,
                'message' => 'Student not assigned to a cohort yet.',
            ]);
        }

        return response()->json([
            'data' => [
                'cohort_id' => $member->cohort_id,
                'cohort_name' => $member->cohort?->name,
                'status' => $member->status,
                'source' => $member->source,
                'assigned_at' => $member->assigned_at,
                'calendar_template_id' => $member->cohort?->calendar_template_id,
                'calendar_name' => $member->cohort?->calendarTemplate?->name,
            ],
        ]);
    }
}
