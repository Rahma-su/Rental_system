<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('dashboard_stats', function (Blueprint $table) {
        $table->id();
        $table->integer('properties');
        $table->integer('rooms');
        $table->integer('tenants');
        $table->integer('maintenance');
        $table->integer('progress');
        $table->year('year');
        $table->json('data'); // store chart data
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_stats');
    }
};
