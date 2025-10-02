<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Exports\UnitsExport;
use Maatwebsite\Excel\Facades\Excel;

class UnitController extends Controller
{
    // Show all units and search 
    public function index(Request $request)
   {
    $query = Unit::query();

    if ($request->has('search')) {
        $search = $request->search;
        $query->where('unit_name', 'like', "%{$search}%")
              ->orWhere('monthly_rent', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%");
    }

    $units = $query->orderBy('unit_name')->get();
    return response()->json($units);
}

    // Show single unit
    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json($unit);
    }

    // Create a new unit
    public function store(Request $request)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'size_sqm' => 'nullable|numeric',
            'type' => 'required|in:Office,Studio,Shop,Store,Locker,Other', 
            'with_vat' => 'boolean',
            'monthly_rent' => 'nullable|numeric',
            'security_deposit' => 'nullable|numeric',
            'water_and_electric' => 'nullable|numeric',
            'water' => 'nullable|numeric',
            'electric' => 'nullable|numeric',
            'parking' => 'nullable|numeric',
            'other' => 'nullable|string|max:255',
            'lease_term' => 'nullable|string|max:255',
            'agreement' => 'nullable|string|max:255',
            'status' => 'nullable|in:available,occupied,maintenance',
        ]);

        $unit = Unit::create($request->all());

        return response()->json([
            'message' => 'Unit created successfully',
            'unit' => $unit
        ], 201);
    }

    // Update a unit
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'unit_name' => 'sometimes|required|string|max:255',
            'size_sqm' => 'nullable|numeric',
            'type' => 'required|in:Office,Studio,Shop,Store,Locker,Other',
            'with_vat' => 'boolean',
            'monthly_rent' => 'nullable|numeric',
            'security_deposit' => 'nullable|numeric',
            'water_and_electric' => 'nullable|numeric',
            'water' => 'nullable|numeric',
            'electric' => 'nullable|numeric',
            'parking' => 'nullable|numeric',
            'other' => 'nullable|string|max:255',
            'lease_term' => 'nullable|string|max:255',
            'agreement' => 'nullable|string|max:255',
            'status' => 'nullable|in:available,occupied,maintenance',
        ]);

        $unit->update($request->all());

        return response()->json([
            'message' => 'Unit updated successfully',
            'unit' => $unit
        ]);
    }

    // Delete a unit
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return response()->json([
            'message' => 'Unit deleted successfully'
        ]);
    }
    public function export()
    {
    return Excel::download(new UnitsExport, 'units.xlsx');
    }
    
    
}