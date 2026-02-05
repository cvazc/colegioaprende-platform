<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubjectStatus;
use App\Http\Controllers\Controller;
use App\Models\ControlSubjectStudent;
use App\Models\Student;
use App\Models\StudentCreditTransaction;
use App\Models\StudentCreditWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentSubjectUnlockController extends Controller
{
    public function store(Request $request, int $subjectId)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $control = ControlSubjectStudent::query()
            ->where('student_id', $user->id)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$control) {
            return response()->json(['message' => 'Subject not found for student.'], 404);
        }

        if ($control->subject_status === SubjectStatus::Enabled ||
            $control->subject_status === SubjectStatus::Approved) {
            return response()->json([
                'message' => 'Subject already enabled.',
            ], 409);
        }

        $wallet = StudentCreditWallet::query()
            ->where('student_id', $user->id)
            ->first();

        if (!$wallet || $wallet->balance < 1) {
            return response()->json([
                'message' => 'Insufficient credits.',
            ], 422);
        }

        DB::transaction(function () use ($wallet, $control, $user, $subjectId) {
            $wallet->balance -= 1;
            $wallet->save();

            StudentCreditTransaction::query()->create([
                'student_id' => $user->id,
                'payment_id' => null,
                'delta' => -1,
                'reason' => 'unlock_subject',
                'meta' => [
                    'subject_id' => $subjectId,
                ],
            ]);

            $control->subject_status = SubjectStatus::Enabled;
            $control->save();
        });

        return response()->json([
            'data' => [
                'subject_id' => $control->subject_id,
                'status' => $control->subject_status->name,
                'status_value' => $control->subject_status->value,
                'credits_left' => $wallet->balance,
            ],
        ]);
    }
}
