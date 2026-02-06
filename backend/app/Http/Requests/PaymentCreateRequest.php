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
            'item_code' => ['required', 'string', 'max:50'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];

        if (config('services.recaptcha.enabled')) {
            $rules['recaptcha_token'] = ['required', 'string', new RecaptchaToken()];
        }

        return $rules;
    }
}
