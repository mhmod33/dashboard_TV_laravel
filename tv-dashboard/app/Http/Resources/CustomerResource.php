<?php

namespace App\Http\Resources;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->customer_name,
            'address' => $this->address,
            'phone' => $this->phone,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'owner' => Admin::where('id', $this->admin_id)->first()->name,
            'created_at' => $this->created_at,
        ];
    }
}
