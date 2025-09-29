<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    // List all wallets
    public function index()
    {
        return Wallet::with(['tenant', 'transactions'])->get();
    }

    // Show single wallet
    public function show($id)
    {
        return Wallet::with(['tenant', 'transactions'])->findOrFail($id);
    }

    // Create wallet
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'balance' => 'nullable|numeric',
            'deposit_balance' => 'nullable|numeric',
        ]);

        return Wallet::create($request->all());
    }

    // Update wallet balances
    public function update(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        $request->validate([
            'balance' => 'nullable|numeric',
            'deposit_balance' => 'nullable|numeric',
        ]);

        $wallet->update($request->all());
        return $wallet;
    }

    // Delete wallet
    public function destroy($id)
    {
        Wallet::findOrFail($id)->delete();
        return response()->json(['message' => 'Wallet deleted successfully']);
    }
    
   public function getByTenant($tenant_id)
{
    $wallet = Wallet::where('tenant_id', $tenant_id)->first();
    if (!$wallet) return response()->json(['message' => 'Wallet not found'], 404);
    return response()->json($wallet);
}

    // Add transaction to wallet
    public function addTransaction(Request $request, $walletId)
    {
        $wallet = Wallet::findOrFail($walletId);

        $request->validate([
            'lease_id' => 'nullable|exists:leases,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'reference_number' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $transaction = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'lease_id' => $request->lease_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'debited' => $request->type === 'debit',
            'description' => $request->description,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'reference_number' => $request->reference_number,
            'remarks' => $request->remarks,
        ]);

        // Update wallet balance automatically
        if ($request->type === 'credit') {
            $wallet->balance += $request->amount;
        } else {
            $wallet->balance -= $request->amount;
        }
        $wallet->save();

        return response()->json($transaction, 201);
    }

}
