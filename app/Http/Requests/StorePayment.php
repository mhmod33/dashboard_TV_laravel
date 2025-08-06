<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayment extends FormRequest
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
            'payment_id' => 'required',
            'serial_number' => ['required', 'exists:customers,serial_number', 'regex:/^[A-Za-z0-9]+$/', 'min:12'],
            'customer_name' => ['required', 'exists:customers,customer_name'],
            'owner' => ['required', 'exists:admins,name'],
            'exp_after' => 'required',
            'exp_before' => 'required',
            'cost' => 'required',
            'duration' => 'required'
        ];
    }
}
