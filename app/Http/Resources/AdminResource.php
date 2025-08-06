<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomerResource;
class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customers=Customer::where('admin_id', $this->id)->get();
        $admin=Admin::find($this->id);
        if($admin->role=='superadmin'){
            return [
                'id'=>$this->id,
                'name' => $this->name,
                'role' => $this->role,
                'status' => $this->status,
                'balance' => $this->balance,
                'customers' =>'this is a super admin does not have any customers!',
            ];
        }
        return [
            'id'=>$this->id,
            'name' => $this->name,
            'role' => $this->role,
            'status' => $this->status,
            'balance' => $this->balance,
            'customers' =>CustomerResource::collection($customers),
        ];
    }
}
