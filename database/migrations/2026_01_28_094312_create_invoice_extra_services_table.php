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
        Schema::create('invoice_extra_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id');
            $table->foreignId('branch_id');

            $table->foreignId('extra_service_id');
            $table->string('extra_service_title')->nullable();          // Snapshot


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
        Schema::dropIfExists('invoice_extra_services');
    }
};