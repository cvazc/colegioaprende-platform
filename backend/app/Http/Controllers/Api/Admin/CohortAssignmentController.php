<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStudentCohortAssignRequest;
use App\Models\Cohort;
use App\Models\Employee;
use App\Models\Student;
use App\Services\CohortAssignmentService;

class CohortAssignmentController extends Controller
{
    public function assignStudent(AdminStudentCohortAssignRequest $request, int $studentId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $student = Student::query()->find($studentId);
        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $cohort = Cohort::query()->find($request->validated()['cohort_id']);
        if (!$cohort || !$cohort->is_active) {
            return response()->json(['message' => 'Cohort not found or inactive.'], 422);
        }

        $member = app(CohortAssignmentService::class)->assignManually(
            $student,
            $cohort,
            $user,
            $request->validated()['reason'] ?? null,
            (bool) ($request->validated()['force'] ?? false)
        );

        return response()->json([
            'data' => [
                'student_id' => $member->student_id,
                'cohort_id' => $member->cohort_id,
                'status' => $member->status,
                'source' => $member->source,
                'assigned_at' => $member->assigned_at,
            ],
        ]);
    }
}
