<?php

namespace App\Http\Resources;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'serial_number' => $this->serial_number,
            'payment_id' => $this->payment_id,
            'owner' => $this->owner,
            'customer_name' => $this->customer_name,
            'date' => $this->created_at,
            'exp_before' => $this->exp_before,
            'exp_after' => $this->exp_after,
            'cost' => $this->cost,
        ];
    }
}
