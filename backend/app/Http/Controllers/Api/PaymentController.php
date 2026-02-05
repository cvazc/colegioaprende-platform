<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentCreateRequest;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Prospect;
use App\Models\Student;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function store(PaymentCreateRequest $request, int $prospectId): JsonResponse
    {
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

        $payment = Payment::query()->create([
            'prospect_id' => $prospect->id,
            'student_id' => Student::query()->where('prospect_id', $prospect->id)->value('id'),
            'provider' => $request->validated()['provider'],
            'status' => 'pending',
            'item_code' => $item->code,
            'amount' => $item->amount,
            'currency' => $item->currency,
        ]);

        $providerResponse = null;
        if ($payment->provider === 'mercadopago') {
            $providerResponse = app(MercadoPagoService::class)->createPreference($payment, $prospect, $item);

            if (!empty($providerResponse['error'])) {
                return response()->json([
                    'message' => 'Mercado Pago preference creation failed.',
                    'details' => $providerResponse,
                ], 502);
            }

            $payment->provider_reference_id = $providerResponse['id'] ?? null;
            $payment->provider_payload = $providerResponse;
            $payment->save();
        }

        return response()->json([
            'data' => [
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'provider' => $payment->provider,
                'provider_reference_id' => $payment->provider_reference_id,
                'init_point' => $providerResponse['init_point'] ?? null,
                'sandbox_init_point' => $providerResponse['sandbox_init_point'] ?? null,
            ],
        ], 201);
    }
}
