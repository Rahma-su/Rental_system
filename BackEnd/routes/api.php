<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\LeaseController;
use App\Http\Controllers\Api\TenantBillController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TenantFormController;

Route::apiResource('tenantform', TenantFormController::class);

Route::apiResource('wallets', WalletController::class);

// Add transaction route
Route::post('wallets/{wallet}/transactions', [WalletController::class, 'addTransaction']);
use App\Http\Controllers\Api\TenantBillPaymentController;


Route::get('/tenant-bills/{bill}/transactions', [TenantBillPaymentController::class, 'billTransactions']);
Route::post('/tenant-bills/{billId}/pay', [TenantBillPaymentController::class, 'payBill']);



Route::apiResource('tenant-bills', TenantBillController::class);
Route::post('/tenant-bills/generate-for-lease', [TenantBillController::class, 'generateForLease']);

Route::apiResource('leases', LeaseController::class);
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::apiResource('units', UnitController::class);
Route::apiResource('payments', PaymentController::class);

Route::get('/wallets/tenant/{tenant_id}', [WalletController::class, 'getByTenant']);
