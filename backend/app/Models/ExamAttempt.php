<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $table = 'exam_attempts';

    protected $fillable = [
        'student_id',
        'subject_id',
        'attempt_type',
        'status',
        'total_questions',
        'correct_count',
        'score',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'correct_count' => 'integer',
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(ExamAttemptQuestion::class, 'attempt_id');
    }
}
