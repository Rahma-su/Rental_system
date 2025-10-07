<?php

namespace App\Http\Controllers\Api;
use App\Notifications\BillGeneratedNotification;
use Illuminate\Support\Facades\Notification;

use App\Http\Controllers\Controller;
use App\Models\TenantBill;
use Illuminate\Http\Request;

class TenantBillController extends Controller
{
    // List all bills
   
     public function index()
    {
    $bills = TenantBill::with(['lease.unit', 'lease.tenant', 'user'])->get();
    return response()->json($bills);
    }

    public function show($id)
    {
    $bill = TenantBill::with(['lease.unit', 'lease.tenant', 'user'])->findOrFail($id);
    return response()->json($bill);
   }

    // Create a new bill
    public function store(Request $request)
    {
        $request->validate([
            'lease_id' => 'required|exists:leases,id',
           
            'amount' => 'required|numeric',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after_or_equal:billing_period_start',
            'billing_type' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'billing_year' => 'nullable|integer',
            'billing_month' => 'nullable|integer',
            'days_used' => 'nullable|integer',
        ]);

        $bill = TenantBill::create([
            'lease_id' => $request->lease_id,
            'user_id' => auth()->id(), // logged-in admin
            'amount' => $request->amount,
            'billing_period_start' => $request->billing_period_start,
            'billing_period_end' => $request->billing_period_end,
            'billing_type' => $request->billing_type,
            'status' => $request->status ?? 'pending',
            'billing_year' => $request->billing_year,
            'billing_month' => $request->billing_month,
            'days_used' => $request->days_used,
        ]);


      $lease = \App\Models\Lease::with('tenant')->find($request->lease_id);

// Make sure the tenant relationship exists and returns TenantForm
// $tenant = $lease->tenant;

// if ($tenant) {
//     // Fire notification with the newly created bill
//     $tenant->notify(new \App\Notifications\BillGeneratedNotification($bill));
// }

return response()->json([
    'message' => 'Tenant bill created successfully',
    'bill' => $bill
], 201);

    }

    // Update a bill
    public function update(Request $request, $id)
    {
        $bill = TenantBill::findOrFail($id);

        $request->validate([
            'lease_id' => 'sometimes|required|exists:leases,id',
            'amount' => 'sometimes|required|numeric',
            'billing_period_start' => 'sometimes|required|date',
            'billing_period_end' => 'sometimes|required|date|after_or_equal:billing_period_start',
            'billing_type' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'billing_year' => 'nullable|integer',
            'billing_month' => 'nullable|integer',
            'days_used' => 'nullable|integer',
        ]);

        $bill->update($request->all());

        return response()->json([
            'message' => 'Tenant bill updated successfully',
            'bill' => $bill
        ]);
    }

    

public function destroy($id)
{
    $bill = TenantBill::findOrFail($id);
    $bill->delete(); // This will now soft delete the bill
    return response()->json(['message' => 'Bill deleted successfully']);
}

   public function generateForLease(Request $request)
{
    $request->validate([
        'lease_id' => 'required|exists:leases,id',
        'year' => 'required|integer',
        'month' => 'required|integer|min:1|max:12',
        'months_count' => 'required|integer|min:1|max:12',
        'billing_type' => 'required|string|in:rent,water_and_electric,water,electric,parking,other'
    ]);

    $lease = \App\Models\Lease::with('unit')->findOrFail($request->lease_id);
    $unit = $lease->unit;

    // Determine monthly amount based on billing type
    switch ($request->billing_type) {
        case 'rent': $monthlyAmount = $unit->monthly_rent ?? 0; break;
        case 'water_and_electric': $monthlyAmount = $unit->water_and_electric ?? 0; break;
        case 'water': $monthlyAmount = $unit->water ?? 0; break;
        case 'electric': $monthlyAmount = $unit->electric ?? 0; break;
        case 'parking': $monthlyAmount = $unit->parking ?? 0; break;
        case 'other': $monthlyAmount = $unit->other ?? 0; break;
        default: $monthlyAmount = 0;
    }

    $monthsToGenerate = [];
    $skippedMonths = [];

    // 🔁 Loop through all requested months
    for ($i = 0; $i < $request->months_count; $i++) {
        $billMonth = $request->month + $i;
        $billYear = $request->year;

        if ($billMonth > 12) {
            $billMonth -= 12;
            $billYear++;
        }

        // Check if bill already exists for this month
        $exists = \App\Models\TenantBill::where('lease_id', $lease->id)
            ->where('billing_type', $request->billing_type)
            ->where('billing_year', $billYear)
            ->where('billing_month', $billMonth)
            ->exists();

        if ($exists) {
            $skippedMonths[] = "{$billYear}-" . str_pad($billMonth, 2, '0', STR_PAD_LEFT);
        } else {
            $monthsToGenerate[] = ['year' => $billYear, 'month' => $billMonth];
        }
    }

    // ⚠️ If all were duplicates
    // if (count($monthsToGenerate) === 0) {
    //     return response()->json([
    //         'message' => 'No new bills generated. All selected months already have a bill of this type.',
    //         'skipped_months' => $skippedMonths
    //     ], 200);
    // }
    if (count($monthsToGenerate) < $request->months_count) {
    return response()->json([
        'message' => 'Some months already have a bill of this type. No bills were generated.',
        'skipped_months' => collect(range(0, $request->months_count - 1))
            ->map(function ($i) use ($request) {
                $m = $request->month + $i;
                $y = $request->year;
                if ($m > 12) { $m -= 12; $y++; }
                return sprintf('%04d-%02d', $y, $m);
            })
            ->toArray(),
    ], 400);
}


    // 📅 Create new bill covering the missing months
    $startMonth = $monthsToGenerate[0];
    $endMonth = end($monthsToGenerate);
    $totalMonths = count($monthsToGenerate);
    $totalAmount = $monthlyAmount * $totalMonths;

    $bill = \App\Models\TenantBill::create([
        'lease_id' => $lease->id,
        'user_id' => auth()->id(),
        'tenant_id' => $lease->tenant_id,
        'amount' => $totalAmount,
        'months_count' => $totalMonths,
        'paid_amount' => 0,
        'paid_months' => 0,
        'billing_period_start' => "{$startMonth['year']}-{$startMonth['month']}-01",
        'billing_period_end' => date("Y-m-t", strtotime("{$endMonth['year']}-{$endMonth['month']}-01")),
        'billing_type' => $request->billing_type,
        'status' => 'pending',
        'billing_year' => $startMonth['year'],
        'billing_month' => $startMonth['month'],
        'days_used' => null,
    ]);
$lease = \App\Models\Lease::with('tenant')->find($request->lease_id);
$tenant = $lease->tenant;

if ($tenant) {
    event(new \App\Events\BillGeneratedEvent($tenant, $bill));
}

    return response()->json([
        'message' => 'Bill generated successfully',
        'bill' => $bill,
        'generated_months' => array_map(fn($m) => "{$m['year']}-" . str_pad($m['month'], 2, '0', STR_PAD_LEFT), $monthsToGenerate),
        'skipped_months' => $skippedMonths,
        'per_month_amount' => $monthlyAmount,
        'remaining' => $totalAmount,
    ]);
   
}

}   
 
