<?php

namespace App\Http\Controllers\Api\Student;

use App\Enums\StudentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentOnboardingUpdateRequest;
use App\Models\Student;
use App\Services\CohortAssignmentService;

class StudentOnboardingController extends Controller
{
    public function update(StudentOnboardingUpdateRequest $request)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $user->fill($request->validated());
        $user->profile_completed_at = now();
        $user->status = StudentStatus::InProgress;
        $user->save();

        app(CohortAssignmentService::class)->assignAutomatically($user, 'onboarding_completed');

        return response()->json([
            'data' => [
                'student_id' => $user->id,
                'status' => $user->status?->value,
                'profile_completed_at' => $user->profile_completed_at,
            ],
        ]);
    }
}
