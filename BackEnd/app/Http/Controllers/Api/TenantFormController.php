<?php


namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantForm;

class TenantFormController extends Controller
{
    public function index()
{
    $tenants = TenantForm::all(); // fetch all tenants from DB
    return response()->json($tenants);
}

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
}
