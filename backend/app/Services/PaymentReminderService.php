<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\PaymentReminderLog;
use Illuminate\Support\Facades\Mail;

class PaymentReminderService
{
    public function sendDueSoonReminders(int $daysAhead = 3): int
    {
        $daysAhead = max(0, $daysAhead);
        $targetDate = now()->addDays($daysAhead)->toDateString();
        $reminderDate = now()->toDateString();
        $sentCount = 0;

        $events = CalendarEvent::query()
            ->whereDate('due_date', $targetDate)
            ->where('is_payment', true)
            ->where('is_active', true)
            ->with(['calendarTemplate.assignments.student'])
            ->get();

        foreach ($events as $event) {
            $assignments = $event->calendarTemplate?->assignments ?? collect();

            foreach ($assignments as $assignment) {
                $student = $assignment->student;
                if (!$student || empty($student->email)) {
                    continue;
                }

                $alreadySent = PaymentReminderLog::query()
                    ->where('student_id', $student->id)
                    ->where('calendar_event_id', $event->id)
                    ->whereDate('reminder_date', $reminderDate)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                try {
                    $dueDate = optional($event->due_date)->format('Y-m-d') ?? $targetDate;
                    $message = "Hola {$student->first_name},\n\n"
                        . "Te recordamos que tienes un pago programado próximamente.\n"
                        . "Concepto: {$event->title}\n"
                        . "Fecha límite: {$dueDate}\n\n"
                        . "Colegio Aprende";

                    Mail::raw($message, function ($mail) use ($student, $event) {
                        $mail->to($student->email)
                            ->subject('Recordatorio de pago - ' . $event->title);
                    });

                    PaymentReminderLog::query()->create([
                        'student_id' => $student->id,
                        'calendar_event_id' => $event->id,
                        'reminder_date' => $reminderDate,
                        'sent_at' => now(),
                    ]);

                    $sentCount++;
                } catch (\Throwable $exception) {
                    report($exception);
                }
            }
        }

        return $sentCount;
    }
}
