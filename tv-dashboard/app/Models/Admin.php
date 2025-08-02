<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Admin extends Authenticatable
{
    protected $table='admins';
   /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens ,HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'password',
        'role'
    ];
}
