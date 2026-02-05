<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentWebhook;
use App\Services\MercadoPagoService;
use App\Services\PaymentService;
use App\Services\PayPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, string $provider): JsonResponse
    {
        $provider = strtolower($provider);

        if (!in_array($provider, ['mercadopago', 'paypal'], true)) {
            return response()->json(['message' => 'Unsupported provider.'], 400);
        }

        $payload = $request->all();

        $eventType = data_get($payload, 'type')
            ?? data_get($payload, 'event_type')
            ?? data_get($payload, 'action');

        $providerEventId = data_get($payload, 'id')
            ?? data_get($payload, 'event_id');

        PaymentWebhook::query()->create([
            'provider' => $provider,
            'event_type' => $eventType,
            'provider_event_id' => $providerEventId,
            'payload' => $payload,
        ]);

        if ($provider === 'mercadopago') {
            $providerPaymentId = data_get($payload, 'data.id')
                ?? data_get($payload, 'resource')
                ?? data_get($payload, 'payment_id')
                ?? data_get($payload, 'id');

            if ($providerPaymentId) {
                $paymentDetails = app(MercadoPagoService::class)->fetchPayment((string) $providerPaymentId);

                $externalReference = data_get($paymentDetails, 'external_reference');
                $status = data_get($paymentDetails, 'status');

                if ($externalReference && $status) {
                    $payment = Payment::query()->find((int) $externalReference);

                    if ($payment && $payment->provider === 'mercadopago') {
                        $payment->provider_payment_id = (string) $providerPaymentId;
                        $payment->status = $this->normalizeStatus((string) $status);
                        $payment->provider_payload = $paymentDetails;
                        $payment->save();

                        app(PaymentService::class)->applyApprovedPayment($payment);
                    }
                }
            }
        }

        if ($provider === 'paypal') {
            $orderId = data_get($payload, 'resource.id') ?? data_get($payload, 'id');
            if ($orderId) {
                $order = app(PayPalService::class)->fetchOrder((string) $orderId);
                $status = data_get($order, 'status');
                $referenceId = data_get($order, 'purchase_units.0.reference_id');

                if ($referenceId && $status) {
                    $payment = Payment::query()->find((int) $referenceId);

                    if ($payment && $payment->provider === 'paypal') {
                        $payment->provider_payment_id = (string) $orderId;
                        $payment->status = $this->normalizeStatus((string) $status);
                        $payment->provider_payload = $order;
                        $payment->save();

                        app(PaymentService::class)->applyApprovedPayment($payment);
                    }
                }
            }
        }

        return response()->json(['message' => 'Webhook received.']);
    }

    private function normalizeStatus(string $status): string
    {
        $status = strtolower($status);

        return match ($status) {
            'approved', 'paid', 'succeeded', 'success' => 'approved',
            'rejected', 'failed', 'declined' => 'rejected',
            'canceled', 'cancelled', 'voided' => 'canceled',
            default => 'pending',
        };
    }
}
