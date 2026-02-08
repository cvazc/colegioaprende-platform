<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cohort extends Model
{
    protected $table = 'cohorts';

    protected $fillable = [
        'name',
        'course_type',
        'enrollment_type',
        'calendar_template_id',
        'capacity',
        'starts_at',
        'ends_at',
        'auto_assign',
        'is_active',
        'created_by_employee_id',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'auto_assign' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function calendarTemplate(): BelongsTo
    {
        return $this->belongsTo(CalendarTemplate::class, 'calendar_template_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CohortMember::class, 'cohort_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(CohortRule::class, 'cohort_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }
}
