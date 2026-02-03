<?php

namespace App\Models;

use App\Enums\SubjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlSubjectStudent extends Model
{
    protected $table = 'control_subject_student';

    public $timestamps = false;

    protected $casts = [
        'subject_status' => SubjectStatus::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    protected $fillable = [
        'student_id',
        'subject_id',
        'exam_id',
        'score',
        'subject_status',
    ];
}
