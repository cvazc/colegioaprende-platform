<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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

        $providerPaymentId = data_get($payload, 'data.id')
            ?? data_get($payload, 'resource')
            ?? data_get($payload, 'payment_id')
            ?? data_get($payload, 'id');

        $status = data_get($payload, 'data.status')
            ?? data_get($payload, 'status');

        if ($providerPaymentId && $status) {
            $normalizedStatus = $this->normalizeStatus((string) $status);

            Payment::query()
                ->where('provider', $provider)
                ->where('provider_payment_id', $providerPaymentId)
                ->update([
                    'status' => $normalizedStatus,
                    'provider_payload' => $payload,
                ]);
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
