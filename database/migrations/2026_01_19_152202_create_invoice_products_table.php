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
        Schema::create('invoice_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id');
            $table->foreignId('branch_id');
            
            $table->foreignId('product_id');
            $table->string('product_name')->nullable();          // Snapshot
            $table->string('product_code')->nullable();

            $table->integer('quantity')->default(1);
            $table->integer('unit_price')->default(0);
            $table->integer('total_price')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_products');
    }
};
