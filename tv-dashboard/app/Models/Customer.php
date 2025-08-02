<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
