<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name');
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->enum('type', ['Office', 'Shop', 'Apartment'])->nullable()->default('Shop');
            $table->boolean('with_vat')->default(false);
            $table->decimal('monthly_rent', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->decimal('water_and_electric', 10, 2)->nullable();
            $table->decimal('water', 10, 2)->nullable();
            $table->decimal('electric', 10, 2)->nullable();
            $table->decimal('parking', 10, 2)->nullable();
            $table->string('other')->nullable();
            $table->string('lease_term')->nullable();
            $table->string('agreement')->nullable();
            $table->enum('status', ['available', 'Occupied', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
