<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCashPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_code' => ['required', 'string', 'max:50'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'reference' => ['sometimes', 'string', 'max:191'],
        ];
    }
}
