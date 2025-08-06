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

    Route::get('getcustomersbyadminId/{id}', [CustomerController::class, 'getCustomersByAdminId']);

    //payments
    Route::apiResource('payments', PaymentController::class);

    // subadmins
    Route::apiResource('subadmins', SubadminController::class);
    Route::post('add-subadmin',[SubadminController::class,'addSubadmin']);
    Route::get('subadminProfile', [SubadminController::class, 'subadminProfile']);

    Route::get('getMySubadmins', [AdminController::class, 'getMySubadmins']);


    // subadmin and customers
    Route::get('myCustomers', [SubadminController::class, 'getMyCustomers']);
    Route::post('myCustomers', [SubadminController::class, 'addMyCustomer']);
    Route::put('myCustomers/{id}', [SubadminController::class, 'updateMyCustomer']);
    Route::delete('myCustomers/{id}', [SubadminController::class, 'deleteMyCustomer']);
    Route::apiResource('admins', AdminController::class);

});


Route::middleware(['auth:sanctum', 'checkRole'])->prefix('superadmin')->group(function () {

    //periods
    Route::apiResource('periods', PeriodController::class);

    //admins
    Route::put('admins/ban/{id}', [AdminController::class, 'ban']);
    Route::patch('update-balance/{id}', [AdminController::class, 'increaseBalance']);
    Route::patch('decrease-balance/{id}', [AdminController::class, 'decreaseBalance']);
    Route::delete('customer/deleteAll', [CustomerController::class, 'deleteAll']);

    // myprofile
    Route::get('superadminProfile', [AdminController::class, 'getSuperadminProfile']);
});
