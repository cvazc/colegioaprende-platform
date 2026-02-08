<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CohortMember extends Model
{
    protected $table = 'cohort_members';

    protected $fillable = [
        'cohort_id',
        'student_id',
        'assigned_by_employee_id',
        'assigned_at',
        'status',
        'source',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class, 'cohort_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_by_employee_id');
    }
}
