<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'serial_number',
        'id',
        'cost',
        'exp_before',
        'owner',
        'payment_id',
        'exp_after',
        'created_at',
        'customer_name',
        'duration'
    ];
}
