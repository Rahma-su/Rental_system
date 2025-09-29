<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeaseController extends Controller
{
    // Show all leases
    public function index()
    {
        $leases = Lease::with(['unit', 'tenant'])->get();
        return response()->json($leases);
    }

    // Show single lease
    public function show($id)
    {
        $lease = Lease::with(['unit', 'tenant'])->findOrFail($id);
        return response()->json($lease);
    }

    // Create a new lease
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'unit_id' => 'required|exists:units,id',
                'tenant_id' => 'required|exists:tenant_forms,id',
                'lease_start_date' => 'required|date',
                'lease_end_date' => 'required|date|after_or_equal:lease_start_date',
                'monthly_rent' => 'nullable|numeric',
                'security_deposit' => 'nullable|numeric',
                'payment_frequency' => 'nullable|string|max:255',
                'grace_period_days' => 'nullable|integer',
                'late_fee_penalty_percent' => 'nullable|numeric',
                'lease_agreement' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'water_and_electric' => 'nullable|numeric',
                'water' => 'nullable|numeric',
                'electric' => 'nullable|numeric',
                'parking' => 'nullable|numeric',
                'other' => 'nullable|string',
            ], [
                // Custom messages for required fields
                'unit_id.required' => 'Please select a unit.',
                'unit_id.exists' => 'The selected unit does not exist.',
                'tenant_id.required' => 'Please select a tenant.',
                'tenant_id.exists' => 'The selected tenant does not exist.',
                'lease_start_date.required' => 'Lease start date is required.',
                'lease_end_date.required' => 'Lease end date is required.',
                'lease_end_date.after_or_equal' => 'Lease end date must be after or equal to start date.',
            ]);

            $lease = Lease::create($validated);

            return response()->json([
                'message' => 'Lease created successfully',
                'lease' => $lease
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Update a lease
    public function update(Request $request, $id)
    {
        $lease = Lease::findOrFail($id);

        try {
            $validated = $request->validate([
                'unit_id' => 'sometimes|required|exists:units,id',
                'tenant_id' => $lease->tenant_id, // auto from lease
                'lease_start_date' => 'sometimes|required|date',
                'lease_end_date' => 'sometimes|required|date|after_or_equal:lease_start_date',
                'monthly_rent' => 'nullable|numeric',
                'security_deposit' => 'nullable|numeric',
                'payment_frequency' => 'nullable|string|max:255',
                'grace_period_days' => 'nullable|integer',
                'late_fee_penalty_percent' => 'nullable|numeric',
                'lease_agreement' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'water_and_electric' => 'nullable|numeric',
                'water' => 'nullable|numeric',
                'electric' => 'nullable|numeric',
                'parking' => 'nullable|numeric',
                'other' => 'nullable|string',
            ]);

            $lease->update($validated);

            return response()->json([
                'message' => 'Lease updated successfully',
                'lease' => $lease
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Delete a lease
    public function destroy($id)
    {
        $lease = Lease::findOrFail($id);
        $lease->delete();

        return response()->json([
            'message' => 'Lease deleted successfully'
        ]);
    }
}
