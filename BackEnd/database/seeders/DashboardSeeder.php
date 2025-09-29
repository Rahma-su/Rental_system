<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardSeeder extends Seeder
{
    public function run()
    {
        DB::table('dashboard_stats')->insert([
            [
                'properties' => 120,
                'rooms' => 350,
                'tenants' => 85,
                'maintenance' => 12,
                'progress' => 45,
                'year' => 2019,
                'data' => json_encode([10, 20, 15, 30, 25, 40, 20, 25, 30])
            ],
            [
                'properties' => 120,
                'rooms' => 350,
                'tenants' => 85,
                'maintenance' => 12,
                'progress' => 45,
                'year' => 2020,
                'data' => json_encode([15, 25, 20, 35, 30, 45, 25, 30, 35])
            ]
        ]);
    }
}
