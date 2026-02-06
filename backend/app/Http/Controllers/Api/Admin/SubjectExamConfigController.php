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
                'allow_open_practice' => $config->allow_open_practice,
            ],
        ]);
    }

    public function update(AdminSubjectExamConfigRequest $request, int $subjectId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $config = SubjectExamConfig::query()->updateOrCreate(
            ['subject_id' => $subjectId],
            $request->validated()
        );

        return response()->json([
            'data' => [
                'subject_id' => $config->subject_id,
                'practice_questions' => $config->practice_questions,
                'midterm_questions' => $config->midterm_questions,
                'final_questions' => $config->final_questions,
                'allow_open_practice' => $config->allow_open_practice,
            ],
        ]);
    }
}
