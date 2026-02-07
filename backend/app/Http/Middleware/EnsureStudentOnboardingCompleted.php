<?php

namespace App\Http\Middleware;

use App\Enums\StudentStatus;
use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentOnboardingCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return $next($request);
        }

        if ($user->profile_completed_at !== null && $user->status !== StudentStatus::OnboardingPending) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Complete your profile before accessing the student dashboard.',
            'required_action' => 'complete_onboarding',
            'onboarding_endpoint' => '/api/student/onboarding',
        ], 409);
    }
}
