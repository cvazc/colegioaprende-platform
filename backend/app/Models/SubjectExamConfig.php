<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectExamConfig extends Model
{
    protected $table = 'subject_exam_configs';

    protected $fillable = [
        'subject_id',
        'practice_questions',
        'midterm_questions',
        'final_questions',
        'allow_open_practice',
    ];

    protected $casts = [
        'practice_questions' => 'integer',
        'midterm_questions' => 'integer',
        'final_questions' => 'integer',
        'allow_open_practice' => 'boolean',
    ];
}
