<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCalendar;
use Illuminate\Http\Request;

class StudentCalendarController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $assignment = StudentCalendar::query()
            ->where('student_id', $user->id)
            ->with(['calendarTemplate.events' => function ($query) {
                $query->where('is_active', true)->orderBy('due_date');
            }])
            ->first();

        if (!$assignment) {
            return response()->json([
                'data' => null,
                'message' => 'Calendar not assigned yet.',
            ]);
        }

        return response()->json([
            'data' => [
                'calendar_template_id' => $assignment->calendar_template_id,
                'calendar_name' => $assignment->calendarTemplate?->name,
                'assigned_start_date' => $assignment->assigned_start_date,
                'events' => $assignment->calendarTemplate?->events?->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'event_code' => $event->event_code,
                        'title' => $event->title,
                        'description' => $event->description,
                        'due_date' => optional($event->due_date)->toDateString(),
                        'is_payment' => $event->is_payment,
                    ];
                })->values() ?? [],
            ],
        ]);
    }
}
