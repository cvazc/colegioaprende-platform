<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCalendar extends Model
{
    protected $table = 'student_calendars';

    protected $fillable = [
        'student_id',
        'calendar_template_id',
        'assigned_by_employee_id',
        'assigned_start_date',
    ];

    protected $casts = [
        'assigned_start_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function calendarTemplate(): BelongsTo
    {
        return $this->belongsTo(CalendarTemplate::class, 'calendar_template_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_by_employee_id');
    }
}
