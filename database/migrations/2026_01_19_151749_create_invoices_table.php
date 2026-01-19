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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // INV-0001
            $table->integer('branch_id');
            $table->dateTime('invoice_datetime');
            
            
            // Totals
            $table->integer('sub_total', 12, 2)->default(0);
            $table->integer('discount', 12, 2)->default(0);
            $table->integer('tax', 12, 2)->default(0);
            $table->integer('grand_total', 12, 2)->default(0);

            // Payment
            // $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            // $table->enum('payment_method', ['cash', 'card', 'transfer'])->nullable();

            // Meta
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
