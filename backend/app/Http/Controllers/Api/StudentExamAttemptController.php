<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamAttemptCreateRequest;
use App\Http\Requests\ExamAttemptSubmitRequest;
use App\Models\ControlSubjectStudent;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptQuestion;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SubjectExamConfig;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentExamAttemptController extends Controller
{
    public function store(ExamAttemptCreateRequest $request, int $subjectId)
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

        if (!in_array($control->subject_status, [SubjectStatus::Enabled, SubjectStatus::Approved], true)) {
            return response()->json(['message' => 'Subject is not enabled.'], 403);
        }

        $validated = $request->validated();
        $attemptType = $validated['attempt_type'];
        $topic = $validated['topic'] ?? null;
        $config = SubjectExamConfig::query()
            ->where('subject_id', $subjectId)
            ->first();

        $defaults = [
            'practice' => 10,
            'midterm' => 20,
            'final' => 40,
        ];

        $configCounts = [
            'practice' => $config?->practice_questions,
            'midterm' => $config?->midterm_questions,
            'final' => $config?->final_questions,
        ];

        $questionCount = (int) ($request->validated()['question_count']
            ?? $configCounts[$attemptType]
            ?? $defaults[$attemptType]
            ?? 10);

        $questionTypes = ['mcq', 'true_false'];
        if ($attemptType === 'practice' && ($config?->allow_open_practice ?? true)) {
            $questionTypes[] = 'open';
        }

        $minDifficulty = $config?->difficulty_min ?? 1;
        $maxDifficulty = $config?->difficulty_max ?? 5;
        if ($minDifficulty > $maxDifficulty) {
            [$minDifficulty, $maxDifficulty] = [$maxDifficulty, $minDifficulty];
        }

        $questionsQuery = Question::query()
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->whereIn('type', $questionTypes)
            ->whereBetween('difficulty', [$minDifficulty, $maxDifficulty]);

        if ($topic) {
            $questionsQuery->where('topic', $topic);
        }

        $questions = $questionsQuery
            ->inRandomOrder()
            ->limit($questionCount)
            ->with(['options' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->get();

        if ($questions->count() < $questionCount) {
            return response()->json([
                'message' => 'Not enough questions available for this subject.',
            ], 422);
        }

        $attempt = DB::transaction(function () use ($user, $subjectId, $attemptType, $questionCount, $questions) {
            $attempt = ExamAttempt::query()->create([
                'student_id' => $user->id,
                'subject_id' => $subjectId,
                'attempt_type' => $attemptType,
                'status' => 'in_progress',
                'total_questions' => $questionCount,
                'correct_count' => 0,
                'score' => null,
                'started_at' => now(),
            ]);

            foreach ($questions as $index => $question) {
                ExamAttemptQuestion::query()->create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'sort_order' => $index + 1,
                    'points' => 0,
                ]);
            }

            return $attempt;
        });

        return response()->json([
            'data' => [
                'attempt_id' => $attempt->id,
                'attempt_type' => $attempt->attempt_type,
                'status' => $attempt->status,
                'total_questions' => $attempt->total_questions,
                'questions' => $questions->map(function (Question $question) {
                    return [
                        'id' => $question->id,
                        'type' => $question->type,
                        'prompt' => $question->prompt,
                        'options' => $question->options->map(function (QuestionOption $option) {
                            return [
                                'id' => $option->id,
                                'label' => $option->label,
                                'text' => $option->text,
                            ];
                        }),
                    ];
                })->values(),
            ],
        ], 201);
    }

    public function show(Request $request, int $attemptId)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $attempt = ExamAttempt::query()
            ->where('id', $attemptId)
            ->where('student_id', $user->id)
            ->with(['questions.question.options' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->first();

        if (!$attempt) {
            return response()->json(['message' => 'Attempt not found.'], 404);
        }

        return response()->json([
            'data' => [
                'attempt_id' => $attempt->id,
                'attempt_type' => $attempt->attempt_type,
                'status' => $attempt->status,
                'score' => $attempt->score,
                'correct_count' => $attempt->correct_count,
                'total_questions' => $attempt->total_questions,
                'questions' => $attempt->questions->map(function (ExamAttemptQuestion $row) {
                    return [
                        'question_id' => $row->question_id,
                        'type' => $row->question?->type,
                        'prompt' => $row->question?->prompt,
                        'answer_option_id' => $row->answer_option_id,
                        'answer_text' => $row->answer_text,
                        'is_correct' => $row->is_correct,
                        'options' => $row->question?->options?->map(function (QuestionOption $option) {
                            return [
                                'id' => $option->id,
                                'label' => $option->label,
                                'text' => $option->text,
                            ];
                        }) ?? [],
                    ];
                })->values(),
            ],
        ]);
    }

    public function submit(ExamAttemptSubmitRequest $request, int $attemptId)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $attempt = ExamAttempt::query()
            ->where('id', $attemptId)
            ->where('student_id', $user->id)
            ->first();

        if (!$attempt) {
            return response()->json(['message' => 'Attempt not found.'], 404);
        }

        if ($attempt->status !== 'in_progress') {
            return response()->json(['message' => 'Attempt is not in progress.'], 409);
        }

        $answers = $request->validated()['answers'];

        DB::transaction(function () use ($attempt, $answers) {
            foreach ($answers as $answer) {
                $row = ExamAttemptQuestion::query()
                    ->where('attempt_id', $attempt->id)
                    ->where('question_id', $answer['question_id'])
                    ->first();

                if (!$row) {
                    continue;
                }

                $question = Question::query()->find($row->question_id);
                if (!$question) {
                    continue;
                }

                $isCorrect = null;
                $points = 0;

                if (in_array($question->type, ['mcq', 'true_false'], true)) {
                    $optionId = $answer['answer_option_id'] ?? null;
                    if ($optionId) {
                        $option = QuestionOption::query()
                            ->where('id', $optionId)
                            ->where('question_id', $question->id)
                            ->first();
                        if ($option) {
                            $isCorrect = (bool) $option->is_correct;
                            $points = $isCorrect ? 1 : 0;
                        }
                    }
                    $row->answer_option_id = $optionId;
                    $row->answer_text = null;
                } else {
                    $row->answer_text = $answer['answer_text'] ?? null;
                    $row->answer_option_id = null;
                }

                $row->is_correct = $isCorrect;
                $row->points = $points;
                $row->answered_at = now();
                $row->save();
            }

            $gradableCount = ExamAttemptQuestion::query()
                ->where('attempt_id', $attempt->id)
                ->whereHas('question', function ($query) {
                    $query->whereIn('type', ['mcq', 'true_false']);
                })
                ->count();

            $correctCount = ExamAttemptQuestion::query()
                ->where('attempt_id', $attempt->id)
                ->where('is_correct', true)
                ->count();

            $answeredCount = ExamAttemptQuestion::query()
                ->where('attempt_id', $attempt->id)
                ->where(function ($query) {
                    $query->whereNotNull('answer_option_id')
                        ->orWhereNotNull('answer_text');
                })
                ->count();

            $attempt->correct_count = $correctCount;
            $attempt->score = $gradableCount > 0 ? round(($correctCount / $gradableCount) * 100, 2) : null;

            if ($answeredCount >= $attempt->total_questions) {
                $attempt->status = 'completed';
                $attempt->completed_at = now();
            }

            $attempt->save();
        });

        return response()->json([
            'data' => [
                'attempt_id' => $attempt->id,
                'status' => $attempt->status,
                'score' => $attempt->score,
                'correct_count' => $attempt->correct_count,
                'total_questions' => $attempt->total_questions,
            ],
        ]);
    }
}
