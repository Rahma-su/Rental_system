<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantForm;
use App\Exports\TenantsExport;
use Maatwebsite\Excel\Facades\Excel;

class TenantFormController extends Controller
{
    // List tenants with optional search
    public function index(Request $request)
    {
        $query = TenantForm::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('full_name', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $tenants = $query->orderBy('full_name')->get();
        return response()->json($tenants);
    }

    // Show single tenant
    public function show($id)
    {
        $tenant = TenantForm::findOrFail($id);
        return response()->json($tenant);
    }

    // Add new tenant
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:tenant_forms,email',
            'national_id' => 'nullable|string|max:50',
            'tin_no' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $tenant = TenantForm::create($data);

        return response()->json(['message' => 'Tenant registered successfully', 'tenant' => $tenant], 201);
    }

    // Update tenant
    public function update(Request $request, $id)
    {
        $tenant = TenantForm::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:tenant_forms,email,' . $id,
            'national_id' => 'nullable|string|max:50',
            'tin_no' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $tenant->update($data);

        return response()->json(['message' => 'Tenant updated successfully', 'tenant' => $tenant]);
    }
     public function export()
    {
    return Excel::download(new TenantsExport, 'tenant_list.xlsx');
    }

    // Delete tenant
    public function destroy($id)
    {
        $tenant = TenantForm::findOrFail($id);
        $tenant->delete();

        return response()->json(['message' => 'Tenant deleted successfully']);
    }
}
