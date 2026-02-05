<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentCreateRequest;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Prospect;
use App\Models\Student;
use App\Services\MercadoPagoService;
use App\Services\PayPalService;
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
            'provider' => $request->validated()['provider'],
            'status' => 'pending',
            'item_code' => $item->code,
            'quantity' => $quantity,
            'amount' => $amount,
            'currency' => $item->currency,
        ]);

        $providerResponse = null;
        if ($payment->provider === 'mercadopago') {
            $providerResponse = app(MercadoPagoService::class)->createPreference(
                $payment,
                $prospect,
                $item,
                $quantity
            );

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

        if ($payment->provider === 'paypal') {
            $providerResponse = app(PayPalService::class)->createOrder(
                $payment,
                $prospect,
                $item,
                $quantity
            );

            if (!empty($providerResponse['error'])) {
                return response()->json([
                    'message' => 'PayPal order creation failed.',
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
                'quantity' => $payment->quantity,
                'provider_reference_id' => $payment->provider_reference_id,
                'init_point' => $providerResponse['init_point'] ?? null,
                'sandbox_init_point' => $providerResponse['sandbox_init_point'] ?? null,
                'approve_url' => $this->resolvePayPalApproveUrl($providerResponse),
            ],
        ], 201);
    }

    private function resolvePayPalApproveUrl(?array $payload): ?string
    {
        if (!$payload || empty($payload['links']) || !is_array($payload['links'])) {
            return null;
        }

        foreach ($payload['links'] as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                return $link['href'] ?? null;
            }
        }

        return null;
    }
}
