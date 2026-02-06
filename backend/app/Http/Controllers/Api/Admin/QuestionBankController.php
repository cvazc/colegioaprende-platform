<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminQuestionStoreRequest;
use App\Models\Employee;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function index(Request $request, int $subjectId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $questions = Question::query()
            ->where('subject_id', $subjectId)
            ->orderByDesc('id')
            ->with('options')
            ->get()
            ->map(function (Question $question) {
                return [
                    'id' => $question->id,
                    'type' => $question->type,
                    'prompt' => $question->prompt,
                    'topic' => $question->topic,
                    'explanation' => $question->explanation,
                    'difficulty' => $question->difficulty,
                    'is_active' => $question->is_active,
                    'options' => $question->options->map(function (QuestionOption $option) {
                        return [
                            'id' => $option->id,
                            'label' => $option->label,
                            'text' => $option->text,
                            'is_correct' => $option->is_correct,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'data' => $questions,
        ]);
    }

    public function store(AdminQuestionStoreRequest $request, int $subjectId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validated();
        $type = $validated['type'];
        $options = $validated['options'] ?? [];

        if ($type !== 'open') {
            if (count($options) < 2) {
                return response()->json([
                    'message' => 'At least two options are required.',
                ], 422);
            }

            $correctCount = collect($options)->where('is_correct', true)->count();
            if ($correctCount !== 1) {
                return response()->json([
                    'message' => 'Exactly one correct option is required.',
                ], 422);
            }
        }

        $question = DB::transaction(function () use ($validated, $subjectId, $options, $type) {
            $question = Question::query()->create([
                'subject_id' => $subjectId,
                'type' => $type,
                'prompt' => $validated['prompt'],
                'topic' => $validated['topic'] ?? null,
                'explanation' => $validated['explanation'] ?? null,
                'difficulty' => $validated['difficulty'] ?? 1,
                'is_active' => true,
            ]);

            if ($type !== 'open') {
                foreach ($options as $index => $option) {
                    QuestionOption::query()->create([
                        'question_id' => $question->id,
                        'label' => $option['label'] ?? null,
                        'text' => $option['text'],
                        'is_correct' => (bool) ($option['is_correct'] ?? false),
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $question;
        });

        return response()->json([
            'data' => [
                'id' => $question->id,
                'type' => $question->type,
                'prompt' => $question->prompt,
                'topic' => $question->topic,
            ],
        ], 201);
    }
}
