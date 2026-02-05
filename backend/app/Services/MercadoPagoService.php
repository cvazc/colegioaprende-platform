<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Prospect;
use Illuminate\Support\Facades\Http;

class MercadoPagoService
{
    public function createPreference(Payment $payment, Prospect $prospect, PaymentItem $item): array
    {
        $accessToken = (string) config('services.mercadopago.access_token');
        $baseUrl = rtrim((string) config('services.mercadopago.base_url'), '/');
        $webhookUrl = (string) (config('services.mercadopago.webhook_url') ?: (config('app.url') . '/api/payments/webhooks/mercadopago'));

        $payload = [
            'external_reference' => (string) $payment->id,
            'notification_url' => $webhookUrl,
            'items' => [
                [
                    'title' => $item->name,
                    'quantity' => 1,
                    'unit_price' => (float) $item->amount,
                    'currency_id' => $item->currency,
                ],
            ],
            'metadata' => [
                'payment_id' => $payment->id,
                'prospect_id' => $prospect->id,
                'item_code' => $item->code,
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post($baseUrl . '/checkout/preferences', $payload);

        if (!$response->ok()) {
            return [
                'error' => true,
                'status' => $response->status(),
                'body' => $response->json(),
            ];
        }

        return $response->json();
    }

    public function fetchPayment(string $providerPaymentId): ?array
    {
        $accessToken = (string) config('services.mercadopago.access_token');
        $baseUrl = rtrim((string) config('services.mercadopago.base_url'), '/');

        $response = Http::withToken($accessToken)
            ->get($baseUrl . '/v1/payments/' . $providerPaymentId);

        if (!$response->ok()) {
            return null;
        }

        return $response->json();
    }
}
