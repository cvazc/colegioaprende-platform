<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Prospect;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    public function createOrder(Payment $payment, Prospect $prospect, PaymentItem $item, int $quantity): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['error' => true, 'message' => 'PayPal access token failed'];
        }

        $baseUrl = rtrim((string) config('services.paypal.base_url'), '/');
        $amount = number_format(((float) $item->amount) * $quantity, 2, '.', '');

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => (string) $payment->id,
                    'amount' => [
                        'currency_code' => $item->currency,
                        'value' => $amount,
                    ],
                    'custom_id' => (string) $payment->id,
                    'description' => $item->name,
                ],
            ],
            'application_context' => [
                'brand_name' => 'Colegio Aprende',
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];

        $response = Http::withToken($token)
            ->post($baseUrl . '/v2/checkout/orders', $payload);

        if (!$response->ok()) {
            return [
                'error' => true,
                'status' => $response->status(),
                'body' => $response->json(),
            ];
        }

        return $response->json();
    }

    public function fetchOrder(string $orderId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        $baseUrl = rtrim((string) config('services.paypal.base_url'), '/');
        $response = Http::withToken($token)
            ->get($baseUrl . '/v2/checkout/orders/' . $orderId);

        if (!$response->ok()) {
            return null;
        }

        return $response->json();
    }

    private function getAccessToken(): ?string
    {
        $clientId = (string) config('services.paypal.client_id');
        $clientSecret = (string) config('services.paypal.client_secret');
        $baseUrl = rtrim((string) config('services.paypal.base_url'), '/');

        if ($clientId === '' || $clientSecret === '') {
            return null;
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post($baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->ok()) {
            return null;
        }

        return $response->json('access_token');
    }
}
