<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    // LoginController.php
    public function login(LoginRequest $request)
    {
        $admin = Admin::where('name', $request->name)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'invalid credentials']);
        }

        // Check if user is active
        if ($admin->status !== 'active') {
            return response()->json([
                'message' => 'Your account is not active. Please contact the administrator.',
                'status' => $admin->status
            ], 403);
        }

        $token = $admin->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'logged in successfully',
            'name' => $admin->name,
            'balance' => $admin->balance,
            'role' => $admin->role,
            'token' => $token,
            'id' => $admin->id,
        ]);
    }
}
