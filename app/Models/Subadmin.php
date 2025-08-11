<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Subadmin extends Model
{
    protected $table = 'admins';
    protected $fillable = [
        'name',
        'password',
        'status',
        'phone',
        'balance',
        'parent_admin_id',
        'role'
    ] ;
    public static function booted()
    {
        static::addGlobalScope('subadminRole',function(Builder $builder){
            $builder->where('role','subadmin');
        });
    }
}
