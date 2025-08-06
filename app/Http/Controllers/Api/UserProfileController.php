<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * Get current user profile with permissions
     */
    public function getProfile(Request $request)
    {
        $user = Auth::user();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ],
            'permissions' => [
                // Pages access permissions based on the image
                'can_access_dashboard' => true, // All roles can access
                'can_access_payment_history' => true, // All roles can access
                'can_access_customers' => true, // All roles can access
                'can_access_admin_users' => $user->isSuperAdmin(), // Only Super Admin
                'can_access_subadmin' => $user->isSuperAdmin() || $user->isAdmin(), // Super Admin and Admin
                'can_access_default_prices' => true, // All roles can access
                'can_access_time_periods' => true, // All roles can access
                'can_access_remove_customer' => true, // All roles can access
                'can_access_delete_all_customers' => $user->isSuperAdmin(), // Only Super Admin
                
                // Actions permissions
                'can_perform_delete_all_customers' => $user->canPerformAction('delete-all-customers'),
                'can_perform_remove_customer' => $user->canPerformAction('remove-customer'),
                'can_perform_manage_default_prices' => $user->canPerformAction('manage-default-prices'),
                'can_perform_manage_time_periods' => $user->canPerformAction('manage-time-periods'),
                'can_perform_manage_admin_users' => $user->canPerformAction('manage-admin-users'),
                'can_perform_manage_subadmin' => $user->canPerformAction('manage-subadmin'),
                
                // Role information
                'is_super_admin' => $user->isSuperAdmin(),
                'is_admin' => $user->isAdmin(),
                'is_subadmin' => $user->isSubAdmin()
            ]
        ]);
    }

    /**
     * Check if user can access a specific page
     */
    public function canAccessPage(Request $request, string $page)
    {
        $user = Auth::user();
        
        return response()->json([
            'can_access' => $user->canAccessPage($page),
            'page' => $page,
            'user_role' => $user->role
        ]);
    }

    /**
     * Check if user can perform a specific action
     */
    public function canPerformAction(Request $request, string $action)
    {
        $user = Auth::user();
        
        return response()->json([
            'can_perform' => $user->canPerformAction($action),
            'action' => $action,
            'user_role' => $user->role
        ]);
    }
}