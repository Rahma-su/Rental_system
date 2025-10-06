<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\LeaseController;
use App\Http\Controllers\Api\TenantBillController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TenantFormController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\AuthController;

Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class,'logout']);
    Route::get('user', [AuthController::class,'me']);
    // other protected APIs go here
});

Route::apiResource('maintenances', MaintenanceController::class);




// exported
Route::get('/unit/export', [UnitController::class, 'export'])->name('unit.export');
Route::get('/lease/export', [LeaseController::class, 'export'])->name('lease.export');
Route::get('/tenantform/export', [TenantFormController::class, 'export'])->name('tenant.export');

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
Route::get('/wallets/{id}/transactions', [WalletController::class, 'getTransactions']);
// routes/api.php


Route::get('/wallets', [WalletController::class, 'index']);
Route::get('/wallets/{id}', [WalletController::class, 'show']);
Route::post('/wallets', [WalletController::class, 'store']);
Route::put('/wallets/{id}', [WalletController::class, 'update']);
Route::delete('/wallets/{id}', [WalletController::class, 'destroy']);

// Wallet transactions
Route::post('/wallets/{walletId}/transactions', [WalletController::class, 'addTransaction']);
Route::get('/wallets/{walletId}/transactions', [WalletController::class, 'getTransactions']); // <- THIS
