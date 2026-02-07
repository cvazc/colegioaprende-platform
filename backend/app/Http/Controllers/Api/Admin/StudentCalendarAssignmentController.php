<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStudentCalendarAssignRequest;
use App\Models\CalendarTemplate;
use App\Models\Employee;
use App\Models\Student;
use App\Models\StudentCalendar;

class StudentCalendarAssignmentController extends Controller
{
    public function assign(AdminStudentCalendarAssignRequest $request, int $studentId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $student = Student::query()->find($studentId);
        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $template = CalendarTemplate::query()->find($request->validated()['calendar_template_id']);
        if (!$template || !$template->is_active) {
            return response()->json(['message' => 'Calendar template not found or inactive.'], 422);
        }

        $assignment = StudentCalendar::query()->updateOrCreate(
            ['student_id' => $student->id],
            [
                'calendar_template_id' => $template->id,
                'assigned_by_employee_id' => $user->id,
                'assigned_start_date' => $request->validated()['assigned_start_date'] ?? null,
            ]
        );

        return response()->json([
            'data' => [
                'student_id' => $assignment->student_id,
                'calendar_template_id' => $assignment->calendar_template_id,
                'assigned_start_date' => $assignment->assigned_start_date,
            ],
        ]);
    }
}
