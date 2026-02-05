<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPaymentReconcileRequest;
use App\Models\Employee;
use App\Models\Payment;
use App\Services\MercadoPagoService;
use App\Services\PaymentService;
use App\Services\PayPalService;

class PaymentReconcileController extends Controller
{
    public function store(AdminPaymentReconcileRequest $request, int $paymentId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $payment = Payment::query()->find($paymentId);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        $providerPaymentId = $request->validated()['provider_payment_id'] ?? null;
        if ($providerPaymentId && $payment->provider_payment_id && $payment->provider_payment_id !== $providerPaymentId) {
            return response()->json([
                'message' => 'Provider payment id does not match existing record.',
            ], 422);
        }

        $paymentDetails = null;
        $provider = $payment->provider;

        if ($provider === 'mercadopago') {
            $mpPaymentId = $providerPaymentId ?? $payment->provider_payment_id;
            if (!$mpPaymentId) {
                return response()->json([
                    'message' => 'provider_payment_id is required for Mercado Pago reconciliation.',
                ], 422);
            }

            $paymentDetails = app(MercadoPagoService::class)->fetchPayment((string) $mpPaymentId);
            if (!$paymentDetails) {
                return response()->json([
                    'message' => 'Unable to fetch Mercado Pago payment.',
                ], 502);
            }

            $status = data_get($paymentDetails, 'status');
            if (!$status) {
                return response()->json([
                    'message' => 'Unable to resolve payment status.',
                ], 422);
            }

            $payment->provider_payment_id = (string) $mpPaymentId;
            $payment->status = $this->normalizeStatus((string) $status);
            $payment->provider_payload = $paymentDetails;
            $payment->save();
        } elseif ($provider === 'paypal') {
            $orderId = $providerPaymentId ?? $payment->provider_payment_id ?? $payment->provider_reference_id;
            if (!$orderId) {
                return response()->json([
                    'message' => 'provider_payment_id is required for PayPal reconciliation.',
                ], 422);
            }

            $paymentDetails = app(PayPalService::class)->fetchOrder((string) $orderId);
            if (!$paymentDetails) {
                return response()->json([
                    'message' => 'Unable to fetch PayPal order.',
                ], 502);
            }

            $status = data_get($paymentDetails, 'status');
            if (!$status) {
                return response()->json([
                    'message' => 'Unable to resolve payment status.',
                ], 422);
            }

            $payment->provider_payment_id = (string) $orderId;
            $payment->status = $this->normalizeStatus((string) $status);
            $payment->provider_payload = $paymentDetails;
            $payment->save();
        } else {
            return response()->json(['message' => 'Unsupported provider.'], 422);
        }

        app(PaymentService::class)->applyApprovedPayment($payment);

        return response()->json([
            'data' => [
                'payment_id' => $payment->id,
                'provider' => $payment->provider,
                'status' => $payment->status,
                'provider_payment_id' => $payment->provider_payment_id,
                'approved_at' => $payment->approved_at?->toDateTimeString(),
            ],
        ]);
    }

    private function normalizeStatus(string $status): string
    {
        $status = strtolower($status);

        return match ($status) {
            'approved', 'paid', 'succeeded', 'success', 'completed' => 'approved',
            'rejected', 'failed', 'declined' => 'rejected',
            'canceled', 'cancelled', 'voided' => 'canceled',
            default => 'pending',
        };
    }
}
