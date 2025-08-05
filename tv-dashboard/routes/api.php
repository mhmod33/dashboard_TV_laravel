<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PeriodController;
use App\Http\Controllers\api\SubadminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Api\CustomerController;

use Illuminate\Support\Facades\Route;

//auth
Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //logout
    Route::post('logout', [LogoutController::class, 'logout']);

    //customers
    Route::apiResource('customers', CustomerController::class);
    Route::get('activeCustomers', [CustomerController::class, 'activeCustomers']);
    Route::get('expiredCustomers', [CustomerController::class, 'expiredCustomers']);
    Route::get('paidCustomers', [CustomerController::class, 'paidCustomers']);
    Route::post('getcustomerbysn', [CustomerController::class, 'getCustomerySn']);
    Route::put('/customers/bulk/payment-status', [CustomerController::class, 'bulkUpdatePaymentStatus']);
    Route::put('/customers/bulk/status', [CustomerController::class, 'bulkUpdateStatus']);


    //payments
    Route::apiResource('payments', PaymentController::class);

    // subadmins
    Route::apiResource('subadmins', SubadminController::class);
    Route::get('subadminProfile', [SubadminController::class, 'subadminProfile']);

    Route::get('getMySubadmins', [AdminController::class, 'getMySubadmins']);


    // subadmin and customers
    Route::get('myCustomers', [SubadminController::class, 'getMyCustomers']);
    Route::post('myCustomers', [SubadminController::class, 'addMyCustomer']);
    Route::put('myCustomers/{id}', [SubadminController::class, 'updateMyCustomer']);
    Route::delete('myCustomers/{id}', [SubadminController::class, 'deleteMyCustomer']);



});


Route::middleware(['auth:sanctum', 'checkRole'])->prefix('superadmin')->group(function () {

    //periods
    Route::apiResource('periods', PeriodController::class);

    //admins
    Route::apiResource('admins', AdminController::class);

    Route::delete('customer/deleteAll', [CustomerController::class, 'deleteAll']);

    // myprofile
    Route::get('superadminProfile', [AdminController::class, 'getSuperadminProfile']);
});
