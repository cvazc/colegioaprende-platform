<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public function isEnabled(): bool
    {
        return (bool) config('services.recaptcha.enabled');
    }

    public function verify(string $token, ?string $ip = null): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        $secret = (string) config('services.recaptcha.secret');
        if ($secret === '' || $token === '') {
            return false;
        }

        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]
        );

        if (!$response->ok()) {
            return false;
        }

        $payload = $response->json();

        if (empty($payload['success'])) {
            return false;
        }

        $minScore = config('services.recaptcha.min_score');
        if (isset($payload['score']) && $minScore !== null) {
            return (float) $payload['score'] >= (float) $minScore;
        }

        return true;
    }
}
