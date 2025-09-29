<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB; // ✅ Add this line

use App\Models\Tenant;
use App\Models\Room;
use App\Models\Rental;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = DB::table('dashboard_stats')->get();
        return response()->json($stats);
    }
}
