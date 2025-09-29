<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenant_forms')->onDelete('cascade');
            $table->date('lease_start_date');
            $table->date('lease_end_date');
            $table->decimal('monthly_rent', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->string('payment_frequency')->nullable(); // monthly, quarterly, etc.
            $table->integer('grace_period_days')->nullable();
            $table->decimal('late_fee_penalty_percent', 5, 2)->nullable();
            $table->string('lease_agreement')->nullable(); // path to agreement file
            $table->text('notes')->nullable();
            $table->decimal('water_and_electric', 10, 2)->nullable();
            $table->decimal('water', 10, 2)->nullable();
            $table->decimal('electric', 10, 2)->nullable();
            $table->decimal('parking', 10, 2)->nullable();
            $table->string('other')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
