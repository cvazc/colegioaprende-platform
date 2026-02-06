<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttemptQuestion extends Model
{
    protected $table = 'exam_attempt_questions';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_option_id',
        'answer_text',
        'is_correct',
        'points',
        'sort_order',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points' => 'integer',
        'sort_order' => 'integer',
        'answered_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function answerOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'answer_option_id');
    }
}
