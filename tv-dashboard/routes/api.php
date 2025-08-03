<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Api\CustomerController;

use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\LoginController;
// use App\Http\Middleware\checkRole;

//auth
Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //logout
    Route::post('logout', [LogoutController::class, 'logout']);

    //customers
    Route::apiResource('customers', CustomerController::class);
    
    //admins
    Route::apiResource('admins', AdminController::class);
    Route::delete('customer/deleteAll', [CustomerController::class, 'deleteAll']);

    //payments
    Route::apiResource('payments', PaymentController::class);
});
// Route::middleware(['auth:sanctum', 'checkRole'])->prefix('admin')->group(function () {
// });
//addresses
// Route::apiResource('addresses', AddressController::class);