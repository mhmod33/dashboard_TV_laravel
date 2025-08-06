<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePeriod extends FormRequest
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
            'period_code' => 'sometimes|alpha_num',
            'display_name' => 'sometimes|string',
            'months' => 'sometimes|integer|min:1',
            'days' => 'sometimes|integer|min:1',
            'display_order' => 'sometimes|integer|min:1',
            'active' => 'sometimes|boolean',
            'price' => 'required|numeric|min:0', // Add this line
            'plan' => 'required|numeric|min:0'
        ];
    }
}
