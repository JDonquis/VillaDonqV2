<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'account_payment_id' => 'required|exists:account_payments,id',
            'amount_in_dolars' => 'required|numeric|min:0',
            'amount_in_bs' => 'required|numeric|min:0',
            'students' => 'required|array|min:1',
        ];
    }
}
