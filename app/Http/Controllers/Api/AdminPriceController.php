<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminPeriodOverride;
use App\Models\Period;
use Illuminate\Http\Request;

class AdminPriceController extends Controller
{
    // Get effective periods (default + overrides) for a specific admin
    public function listForAdmin(int $adminId)
    {
        $currentUser = auth()->user();

        // Authorization: superadmin can view any; admin can view only own subadmins; no self/other-admin management
        if (!$currentUser->isSuperAdmin()) {
            // Admin cannot access another admin or self here
            if ($currentUser->isAdmin()) {
                $targetAdmin = Admin::findOrFail($adminId);
                if ($targetAdmin->role !== Admin::ROLE_SUBADMIN || $targetAdmin->parent_admin_id !== $currentUser->id) {
                    return response()->json(['message' => 'Forbidden'], 403);
                }
            } else {
                // subadmin cannot access
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $periods = Period::all()->keyBy('id');
        $overrides = AdminPeriodOverride::where('admin_id', $adminId)->get();

        $result = $periods->values()->map(function ($period) use ($overrides) {
            $override = $overrides->firstWhere('period_id', $period->id);
            return [
                'id' => $period->id,
                'period_code' => $period->period_code,
                'display_name' => $period->display_name,
                'months' => $period->months,
                'days' => $period->days,
                'display_order' => $period->display_order,
                'active' => $period->active,
                'price' => $override->price ?? $period->price,
                'plan' => $override->plan ?? $period->plan,
            ];
        });

        return response()->json(['periods' => $result]);
    }

    // Upsert overrides for a specific admin
    public function upsertForAdmin(int $adminId, Request $request)
    {
        $currentUser = auth()->user();

        // Authorization: superadmin can update any; admin can update only own subadmins; cannot update self or another admin
        if (!$currentUser->isSuperAdmin()) {
            if ($currentUser->isAdmin()) {
                $targetAdmin = Admin::findOrFail($adminId);
                if ($targetAdmin->role !== Admin::ROLE_SUBADMIN || $targetAdmin->parent_admin_id !== $currentUser->id) {
                    return response()->json(['message' => 'Forbidden'], 403);
                }
            } else {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $validated = $request->validate([
            'overrides' => 'required|array|min:1',
            'overrides.*.period_id' => 'required|exists:periods,id',
            'overrides.*.price' => 'nullable|numeric|min:0',
            'overrides.*.plan' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['overrides'] as $row) {
            AdminPeriodOverride::updateOrCreate(
                ['admin_id' => $adminId, 'period_id' => $row['period_id']],
                ['price' => $row['price'] ?? null, 'plan' => $row['plan'] ?? null]
            );
        }

        return response()->json(['message' => 'Overrides saved']);
    }
}



