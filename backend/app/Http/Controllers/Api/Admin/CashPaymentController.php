<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCashPaymentRequest;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Prospect;
use App\Models\Student;
use App\Services\PaymentService;

class CashPaymentController extends Controller
{
    public function store(AdminCashPaymentRequest $request, int $prospectId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $prospect = Prospect::query()->find($prospectId);
        if (!$prospect) {
            return response()->json(['message' => 'Prospect not found.'], 404);
        }

        $item = PaymentItem::query()
            ->where('course_type', $prospect->course_type)
            ->where('enrollment_type', $prospect->enrollment_type)
            ->where('code', $request->validated()['item_code'])
            ->where('is_active', true)
            ->first();

        if (!$item) {
            return response()->json([
                'message' => 'Payment item not configured for prospect.',
            ], 422);
        }

        if (!$item->is_billable) {
            return response()->json([
                'message' => 'Payment item is included and not billable.',
            ], 422);
        }

        $quantity = (int) ($request->validated()['quantity'] ?? 1);
        if ($item->unlock_all_subjects && $quantity !== 1) {
            return response()->json([
                'message' => 'Quantity is not allowed for this payment item.',
            ], 422);
        }

        $amount = round(((float) $item->amount) * $quantity, 2);

        $payment = Payment::query()->create([
            'prospect_id' => $prospect->id,
            'student_id' => Student::query()->where('prospect_id', $prospect->id)->value('id'),
            'provider' => 'cash',
            'status' => 'approved',
            'item_code' => $item->code,
            'quantity' => $quantity,
            'amount' => $amount,
            'currency' => $item->currency,
            'provider_reference_id' => $request->validated()['reference'] ?? null,
        ]);

        app(PaymentService::class)->applyApprovedPayment($payment);

        return response()->json([
            'data' => [
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'provider' => $payment->provider,
                'quantity' => $payment->quantity,
            ],
        ], 201);
    }
}
