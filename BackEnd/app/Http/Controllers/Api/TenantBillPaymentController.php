<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantBill;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class TenantBillPaymentController extends Controller
{
//     public function payBill(Request $request, $billId)
// {
//     $request->validate([
//         'amount' => 'required|numeric|min:0.01',
//         'payment_method' => 'required|string',
//         'reference_number' => 'required|string',
//         'remarks' => 'nullable|string'
//     ]);

//     $bill = TenantBill::findOrFail($billId);

//     // Check months_count
//     if (!$bill->months_count || $bill->months_count <= 0) {
//         return response()->json(['message' => 'Invalid months count for this bill'], 400);
//     }

//     $wallet = Wallet::where('tenant_id', $bill->tenant_id)->first();
//     if (!$wallet) {
//         return response()->json(['message' => 'Tenant wallet not found'], 404);
//     }

//     $amountToPay = $request->amount;

//     $perMonthAmount = $bill->amount / $bill->months_count;

//     if ($perMonthAmount <= 0) {
//         return response()->json(['message' => 'Bill amount is invalid'], 400);
//     }

//     $maxMonthsPayable = floor(min($amountToPay, $wallet->balance) / $perMonthAmount);

//     if ($maxMonthsPayable < 1) {
//         return response()->json(['message' => 'Insufficient amount for at least one full month'], 400);
//     }

//     $totalPayment = $maxMonthsPayable * $perMonthAmount;

//     // Deduct from wallet
//     $wallet->balance -= $totalPayment;
//     $wallet->save();

//     // Create wallet transaction
//     WalletTransaction::create([
//         'lease_id' => $bill->lease_id,
//         'type' => 'debit',
//         'amount' => $totalPayment,
//         'debited' => 1,
//         'description' => 'Bill Payment',
//         'payment_method' => $request->payment_method,
//         'payment_date' => now(),
//         'reference_number' => $request->reference_number,
//         'remarks' => $request->remarks ?? null,
//         'wallet_id' => $wallet->id,
//     ]);

//     // Update bill
//     $paidMonths = $bill->paid_months ?? 0;
//     $bill->paid_months = $paidMonths + $maxMonthsPayable;

//     $bill->status = $bill->paid_months >= $bill->months_count ? 'paid' : 'partial';
//     $bill->save();

//     return response()->json([
//         'message' => 'Payment successful',
//         'bill' => $bill,
//         'wallet_balance' => $wallet->balance,
//         'months_paid' => $maxMonthsPayable,
//         'total_paid_amount' => $totalPayment
//     ]);
// }
public function payBill(Request $request, $billId)
{
    $request->validate([
        'amount' => 'required|numeric|min:0.01',
        'payment_method' => 'required|string',
        'reference_number' => 'required|string',
        'remarks' => 'nullable|string'
    ]);

    $bill = TenantBill::findOrFail($billId);

    $wallet = Wallet::where('tenant_id', $bill->tenant_id)->first();
    if (!$wallet) {
        return response()->json(['message' => 'Tenant wallet not found'], 404);
    }

    $amountToPay = min($request->amount, $wallet->balance);

    if ($amountToPay <= 0) {
        return response()->json(['message' => 'Insufficient wallet balance'], 400);
    }

    $perMonthAmount = $bill->amount / $bill->months_count;
    $remainingMonths = $bill->months_count - ($bill->paid_months ?? 0);

    // Calculate how many full months can be paid with the amount provided
    $monthsPayable = floor($amountToPay / $perMonthAmount);

    if ($monthsPayable < 1) {
        return response()->json(['message' => 'Amount too low to cover at least one month'], 400);
    }

    $monthsToPay = min($monthsPayable, $remainingMonths);
    $totalPayment = $monthsToPay * $perMonthAmount;

    // Deduct from wallet
    $wallet->balance -= $totalPayment;
    $wallet->save();

    // Create wallet transaction
    WalletTransaction::create([
        'lease_id' => $bill->lease_id,
        'type' => 'debit',
        'amount' => $totalPayment,
        'debited' => 1,
        'description' => 'Bill Payment',
        'payment_method' => $request->payment_method,
        'payment_date' => now(),
        'reference_number' => $request->reference_number,
        'remarks' => $request->remarks ?? null,
        'wallet_id' => $wallet->id,
    ]);

    // Update bill
    $bill->paid_amount = ($bill->paid_amount ?? 0) + $totalPayment;
    $bill->paid_months = ($bill->paid_months ?? 0) + $monthsToPay;
    $bill->status = $bill->paid_months >= $bill->months_count ? 'paid' : 'partial';
    $bill->save();

    return response()->json([
        'message' => 'Payment successful',
        'bill' => $bill,
        'wallet_balance' => $wallet->balance,
        'months_paid_now' => $monthsToPay,
        'amount_paid_now' => $totalPayment,
        'total_paid' => $bill->paid_amount
    ]);
}


}
