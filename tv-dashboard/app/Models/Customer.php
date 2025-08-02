<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
class Customer extends Model
{
    protected $fillable = [
        'serial_number',
        'customer_name',
        'payment_status',
        'plan_id',
        'admin_id',
        'address',
        'phone',
        'status',
    ];
    public function admin(){
        return $this->belongsTo(Admin::class);
    }
}
