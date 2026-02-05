<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentCreateRequest;
use App\Models\Payment;
use App\Models\Pricing;
use App\Models\Prospect;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function store(PaymentCreateRequest $request, int $prospectId): JsonResponse
    {
        $prospect = Prospect::query()->find($prospectId);

        if (!$prospect) {
            return response()->json(['message' => 'Prospect not found.'], 404);
        }

        $pricing = Pricing::query()
            ->where('course_type', $prospect->course_type)
            ->where('enrollment_type', $prospect->enrollment_type)
            ->where('is_active', true)
            ->first();

        if (!$pricing) {
            return response()->json([
                'message' => 'Pricing not configured for prospect.',
            ], 422);
        }

        $payment = Payment::query()->create([
            'prospect_id' => $prospect->id,
            'provider' => $request->validated()['provider'],
            'status' => 'pending',
            'amount' => $pricing->amount,
            'currency' => $pricing->currency,
        ]);

        return response()->json([
            'data' => [
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'provider' => $payment->provider,
            ],
        ], 201);
    }
}
