<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'serial_number',
        'payment_id',
        'owner',
        'customer_name',
        'duration',
        'exp_before',
        'exp_after',
        'cost',
        'created_at',
        'updated_at'
    ];
}
