<?php

namespace App\Http\Requests;

use App\Rules\RecaptchaToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('recaptcha_token') && $this->has('g-recaptcha-response')) {
            $this->merge(['recaptcha_token' => $this->input('g-recaptcha-response')]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'provider' => ['required', 'string', Rule::in(['mercadopago', 'paypal'])],
        ];

        if (config('services.recaptcha.enabled')) {
            $rules['recaptcha_token'] = ['required', 'string', new RecaptchaToken()];
        }

        return $rules;
    }
}
