<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Customer;

class Admin extends Authenticatable
{
    protected $table='admins';
   /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens ,HasFactory, Notifiable;

    // Role constants
    const ROLE_SUPER_ADMIN = 'super admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUBADMIN = 'subadmin';

    protected $fillable = [
        'name',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function customer(){
        return $this->hasMany(Customer::class);
    }

    // Role checking methods
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSubAdmin(): bool
    {
        return $this->role === self::ROLE_SUBADMIN;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    // Permission checking methods
    public function canAccessPage(string $page): bool
    {
        // Super admin can access everything
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Pages accessible by all roles (Super Admin, Admin, Sub Admin)
        $allAccessPages = [
            'dashboard',
            'payment-history',
            'customers',
            'default-prices',
            'time-periods',
            'remove-customer'
        ];

        // Pages accessible only by Super Admin
        $superAdminOnlyPages = [
            'admin-users',
            'delete-all-customers'
        ];

        // Pages accessible by Super Admin and Admin (not Sub Admin)
        $adminAccessPages = [
            'subadmin'
        ];

        if (in_array($page, $superAdminOnlyPages)) {
            return $this->isSuperAdmin();
        }

        if (in_array($page, $adminAccessPages)) {
            return $this->isSuperAdmin() || $this->isAdmin();
        }

        return in_array($page, $allAccessPages);
    }

    public function canPerformAction(string $action): bool
    {
        // Super admin can perform all actions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Actions accessible by all roles (Super Admin, Admin, Sub Admin)
        $allAccessActions = [
            'view-dashboard',
            'view-payment-history',
            'view-customers',
            'manage-customers',
            'view-default-prices',
            'view-time-periods',
            'remove-customer'
        ];

        // Actions accessible only by Super Admin
        $superAdminOnlyActions = [
            'manage-admin-users',
            'delete-all-customers'
        ];

        // Actions accessible by Super Admin and Admin (not Sub Admin)
        $adminAccessActions = [
            'manage-subadmin'
        ];

        if (in_array($action, $superAdminOnlyActions)) {
            return $this->isSuperAdmin();
        }

        if (in_array($action, $adminAccessActions)) {
            return $this->isSuperAdmin() || $this->isAdmin();
        }

        return in_array($action, $allAccessActions);
    }
}
