<?php

namespace App\Rules;

use App\Services\RecaptchaService;
use Illuminate\Contracts\Validation\Rule;

class RecaptchaToken implements Rule
{
    public function passes($attribute, $value): bool
    {
        $service = new RecaptchaService();

        return $service->verify((string) $value, request()->ip());
    }

    public function message(): string
    {
        return 'Invalid reCAPTCHA token.';
    }
}
