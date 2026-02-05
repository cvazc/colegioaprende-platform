<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Student;
use App\Models\StudentCreditTransaction;
use App\Models\StudentCreditWallet;
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

            if ($item && $item->credit_qty > 0) {
                $student = $this->resolveStudent($payment);
                if ($student) {
                    if (!$payment->student_id) {
                        $payment->student_id = $student->id;
                        $payment->save();
                    }
                    $this->addCredits($student->id, $payment->id, $item->credit_qty, 'payment_credit');
                }
            }

            if ($item && $item->auto_register && $payment->prospect) {
                if (!$this->resolveStudent($payment)) {
                    $student = app(ProspectRegistrationService::class)->register($payment->prospect);
                    $payment->student_id = $student->id;
                    $payment->save();
                }
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
}
