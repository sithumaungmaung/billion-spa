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
        Schema::create('customer_prepaid_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no');
            $table->foreignId('customer_info_id');
            $table->foreignId('user_id');
            $table->integer('branch_id');
            $table->decimal('amount', 12, 2);
            $table->string('transaction_type'); //'topup', 'use', 'refund'
            $table->decimal('balance', 12, 2)->default(0);

            $table->string('reference_type')->nullable(); // service / order / invoice
            $table->unsignedBigInteger('reference_id')->nullable(); // invoice Id or something

            $table->dateTime('transaction_date');
            $table->string('payment_method')->nullable(); //kbz , kpay, aya, cash, prepaid
            $table->tinyInteger('status')->default(0); // 1 = paid
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_prepaid_transactions');
    }
};