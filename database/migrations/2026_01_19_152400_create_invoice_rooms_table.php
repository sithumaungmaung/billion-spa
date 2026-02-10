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
        Schema::create('invoice_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id');
            $table->foreignId('branch_id');
            $table->foreignId('room_id');

            $table->tinyInteger('service_type');
            $table->integer('service_type_price')->default(0);
            $table->string('room_name')->nullable();          // Snapshot
            $table->string('room_code')->nullable();

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
        Schema::dropIfExists('invoice_rooms');
    }
};
