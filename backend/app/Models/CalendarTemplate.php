<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarTemplate extends Model
{
    protected $table = 'calendar_templates';

    protected $fillable = [
        'name',
        'description',
        'created_by_employee_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'calendar_template_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(StudentCalendar::class, 'calendar_template_id');
    }

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class, 'calendar_template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }
}
