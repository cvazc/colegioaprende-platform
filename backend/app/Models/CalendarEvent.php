<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarEvent extends Model
{
    protected $table = 'calendar_events';

    protected $fillable = [
        'calendar_template_id',
        'event_code',
        'title',
        'description',
        'due_date',
        'is_payment',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_payment' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function calendarTemplate(): BelongsTo
    {
        return $this->belongsTo(CalendarTemplate::class, 'calendar_template_id');
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(PaymentReminderLog::class, 'calendar_event_id');
    }
}
