<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = [
        'period_code',
        'display_name',
        'months',
        'days',
        'display_order',
        'active',
        'price'
    ];
}
