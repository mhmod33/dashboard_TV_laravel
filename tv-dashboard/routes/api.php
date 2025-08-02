<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\LoginController;
// use App\Http\Middleware\checkRole;

//auth
Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //logout
    // Route::post('logout', [logoutController::class, 'logout']);

   

});
Route::middleware(['auth:sanctum', 'checkRole'])->prefix('admin')->group(function () {
    // Route::get('profile', [AdminProfileController::class, 'index']);
});
//addresses
// Route::apiResource('addresses', AddressController::class);