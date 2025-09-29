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
        Schema::create('wallet_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
    $table->foreignId('lease_id')->nullable()->constrained('leases')->onDelete('set null');
    $table->string('type'); // credit or debit
    $table->decimal('amount', 10, 2);
    $table->boolean('debited')->default(false);
    $table->string('description')->nullable();
    $table->string('payment_method')->nullable();
    $table->date('payment_date')->nullable();
    $table->string('reference_number')->nullable();
    $table->string('remarks')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
