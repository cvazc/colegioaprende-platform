<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Prospect;
use App\Services\ProspectRegistrationService;
use Illuminate\Http\Request;

class ProspectRegistrationController extends Controller
{
    public function registerStudent(Request $request, int $prospectId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $prospect = Prospect::query()->find($prospectId);
        if (!$prospect) {
            return response()->json(['message' => 'Prospect not found.'], 404);
        }

        $student = app(ProspectRegistrationService::class)->register($prospect);

        return response()->json([
            'data' => [
                'student_id' => $student->id,
                'email' => $student->email,
            ],
        ], 201);
    }
}
