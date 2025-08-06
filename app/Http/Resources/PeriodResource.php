<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeriodResource extends JsonResource
{
    public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'period_code' => $this->period_code,
        'display_name' => $this->display_name,
        'months' => $this->months,
        'days' => $this->days,
        'display_order' => $this->display_order,
        'active' => $this->active,
        'price' => $this->price,
        'plan' => $this->plan,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}
}
