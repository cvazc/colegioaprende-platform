<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CohortAssignmentLog extends Model
{
    protected $table = 'cohort_assignment_logs';

    protected $fillable = [
        'student_id',
        'from_cohort_id',
        'to_cohort_id',
        'action',
        'reason',
        'actor_employee_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function fromCohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class, 'from_cohort_id');
    }

    public function toCohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class, 'to_cohort_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'actor_employee_id');
    }
}
