<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized. Please login first.'], 401);
        }

        $user = auth()->user();

        // Check if user can perform the specific action
        if (!$user->canPerformAction($action)) {
            return response()->json([
                'message' => 'You are not authorized to perform this action.',
                'action' => $action,
                'user_role' => $user->role
            ], 403);
        }

        return $next($request);
    }
} 