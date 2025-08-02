<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;

class LogoutController extends Controller
{
    public function logout(Request $request){
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message'=>'logged out successfully']);
}
}
