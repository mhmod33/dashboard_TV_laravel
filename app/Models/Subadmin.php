<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Subadmin extends Model
{
    protected $table = 'admins';

    public static function booted()
    {
        static::addGlobalScope('subadminRole',function(Builder $builder){
            $builder->where('role','subadmin');
        });
    }
}
