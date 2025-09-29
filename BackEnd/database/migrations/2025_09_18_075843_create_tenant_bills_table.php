<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenant_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenant_forms')->onDelete('cascade'); 
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->decimal('amount', 10, 2); // total bill amount
            $table->decimal('paid_amount', 10, 2)->default(0); // track partial payments
            $table->integer('months_count')->default(1); // number of months included in this bill
            $table->integer('paid_months')->default(0); // how many months are already paid

            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->string('billing_type')->nullable();
            $table->enum('status', ['unpaid', 'partial', 'pending', 'paid'])->default('pending');
            $table->integer('billing_year')->nullable();
            $table->integer('billing_month')->nullable();
            $table->integer('days_used')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_bills');
    }
};
