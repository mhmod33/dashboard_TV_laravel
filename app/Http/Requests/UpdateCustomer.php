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
            'serial_number' => ['required', 'min:12', 'regex:/^[A-F0-9]+$/'],
            'customer_name' => 'required',
            'plan_id' => 'required|exists:periods,id', // This ensures the plan exists
            'admin_id' => 'sometimes|exists:admins,id', // Ensure admin exists if provided
            'address' => ['nullable', 'max:255'],
            'phone' => ['nullable', 'min:11'],
            'payment_status' => 'required|in:paid,unpaid' // Ensure valid status
        ];
    }

    public function messages(): array
{
    return [
        'serial_number.regex' => 'The serial number must contain only uppercase letters A-F and numbers (0-9)',
        'customer_name' => 'The customer name is required',
        'plan_id.exists' => 'Invalid plan selected',
        'admin_id.exists' => 'Admin does not exist',
        'payment_status.in' => 'Invalid payment status selected'
    ];
}
}
