<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\ControlSubjectStudent;
use App\Models\Student;
use App\Models\StudentCreditTransaction;
use App\Models\StudentCreditWallet;
use App\Enums\SubjectStatus;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function applyApprovedPayment(Payment $payment): void
    {
        if ($payment->status !== 'approved') {
            return;
        }

        $payment->loadMissing('prospect');

        $item = null;
        if ($payment->item_code) {
            $item = PaymentItem::query()
                ->where('course_type', $payment->prospect?->course_type)
                ->where('enrollment_type', $payment->prospect?->enrollment_type)
                ->where('code', $payment->item_code)
                ->first();
        }

        DB::transaction(function () use ($payment, $item) {
            $payment->approved_at = $payment->approved_at ?: now();
            $payment->save();

            if (!$item) {
                return;
            }

            $student = $this->resolveStudent($payment);
            if (!$student && $item->auto_register && $payment->prospect) {
                $student = app(ProspectRegistrationService::class)->register($payment->prospect);
                $payment->student_id = $student->id;
                $payment->save();
            }

            if (!$student) {
                return;
            }

            $creditsToAdd = $item->credit_qty * max(1, (int) $payment->quantity);
            if ($creditsToAdd > 0) {
                $this->addCredits($student->id, $payment->id, $creditsToAdd, 'payment_credit');
            }

            if ($item->unlock_all_subjects) {
                $this->unlockAllSubjects($student->id, $payment->id, $creditsToAdd);
            }
        });
    }

    private function resolveStudent(Payment $payment): ?Student
    {
        if ($payment->student_id) {
            return Student::query()->find($payment->student_id);
        }

        if ($payment->prospect_id) {
            return Student::query()->where('prospect_id', $payment->prospect_id)->first();
        }

        return null;
    }

    private function addCredits(int $studentId, int $paymentId, int $credits, string $reason): void
    {
        $wallet = StudentCreditWallet::query()->firstOrCreate(
            ['student_id' => $studentId],
            ['balance' => 0]
        );

        $wallet->balance += $credits;
        $wallet->save();

        StudentCreditTransaction::query()->create([
            'student_id' => $studentId,
            'payment_id' => $paymentId,
            'delta' => $credits,
            'reason' => $reason,
        ]);
    }

    private function unlockAllSubjects(int $studentId, int $paymentId, int $creditsAdded): void
    {
        $disabledCount = ControlSubjectStudent::query()
            ->where('student_id', $studentId)
            ->where('subject_status', SubjectStatus::Disabled)
            ->count();

        if ($disabledCount === 0) {
            return;
        }

        ControlSubjectStudent::query()
            ->where('student_id', $studentId)
            ->where('subject_status', SubjectStatus::Disabled)
            ->update(['subject_status' => SubjectStatus::Enabled->value]);

        if ($creditsAdded <= 0) {
            return;
        }

        $wallet = StudentCreditWallet::query()->firstOrCreate(
            ['student_id' => $studentId],
            ['balance' => 0]
        );

        $consume = min($wallet->balance, $disabledCount);
        if ($consume <= 0) {
            return;
        }

        $wallet->balance -= $consume;
        $wallet->save();

        StudentCreditTransaction::query()->create([
            'student_id' => $studentId,
            'payment_id' => $paymentId,
            'delta' => -$consume,
            'reason' => 'unlock_all_subjects',
            'meta' => [
                'subject_count' => $disabledCount,
            ],
        ]);
    }
}
