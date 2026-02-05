<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminSubjectExamConfigRequest;
use App\Models\Employee;
use App\Models\SubjectExamConfig;
use Illuminate\Http\Request;

class SubjectExamConfigController extends Controller
{
    public function show(Request $request, int $subjectId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $config = SubjectExamConfig::query()
            ->where('subject_id', $subjectId)
            ->first();

        if (!$config) {
            return response()->json([
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => [
                'subject_id' => $config->subject_id,
                'practice_questions' => $config->practice_questions,
                'midterm_questions' => $config->midterm_questions,
                'final_questions' => $config->final_questions,
                'difficulty_min' => $config->difficulty_min,
                'difficulty_max' => $config->difficulty_max,
            ],
        ]);
    }

    public function update(AdminSubjectExamConfigRequest $request, int $subjectId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validated();
        if (
            array_key_exists('difficulty_min', $validated) &&
            array_key_exists('difficulty_max', $validated) &&
            $validated['difficulty_min'] > $validated['difficulty_max']
        ) {
            return response()->json([
                'message' => 'difficulty_min must be less than or equal to difficulty_max.',
            ], 422);
        }

        $config = SubjectExamConfig::query()->updateOrCreate(
            ['subject_id' => $subjectId],
            $validated
        );

        return response()->json([
            'data' => [
                'subject_id' => $config->subject_id,
                'practice_questions' => $config->practice_questions,
                'midterm_questions' => $config->midterm_questions,
                'final_questions' => $config->final_questions,
                'difficulty_min' => $config->difficulty_min,
                'difficulty_max' => $config->difficulty_max,
            ],
        ]);
    }
}
