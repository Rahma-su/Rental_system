<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Unit;

class MaintenanceController extends Controller
{
    // List all maintenances (with unit)
    public function index()
    {
        return response()->json(Maintenance::with('unit')->orderBy('maintenance_date','desc')->get());
    }

    // Show single
    public function show($id)
    {
        return response()->json(Maintenance::with('unit')->findOrFail($id));
    }

    // Create new
    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric',
            'maintenance_date' => 'required|date',
            'status' => 'nullable|in:pending,in_progress,completed'
        ]);

        $maintenance = Maintenance::create($data);

        return response()->json(['message' => 'Maintenance created', 'maintenance' => $maintenance], 201);
    }

    // Update
    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        $data = $request->validate([
            'unit_id' => 'sometimes|required|exists:units,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'sometimes|required|numeric',
            'maintenance_date' => 'sometimes|required|date',
            'status' => 'nullable|in:pending,in_progress,completed'
        ]);

        $maintenance->update($data);

        return response()->json(['message' => 'Maintenance updated', 'maintenance' => $maintenance]);
    }

    // Delete
    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();

        return response()->json(['message' => 'Maintenance deleted']);
    }
}
