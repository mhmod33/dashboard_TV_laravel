<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomer extends FormRequest
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
            'serial_number' => ['required', 'min:12', 'regex:/^[A-Za-z0-9]+$/'],
            'customer_name' => 'required',
            'plan_id' => 'required',
            'admin_id' => 'required',
            'address' => ['nullable', 'max:255'],
            'phone' => ['nullable', 'min:11'],
            'payment_status' => 'required'
        ];
    }
}
