<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPeriodOverride extends Model
{
    use HasFactory;

    protected $table = 'admin_period_overrides';

    protected $fillable = [
        'admin_id',
        'period_id',
        'price',
        'plan',
    ];
}


