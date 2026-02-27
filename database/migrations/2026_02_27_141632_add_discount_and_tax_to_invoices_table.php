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
        Schema::table('invoices', function (Blueprint $table) {
            $table->tinyInteger('discount_percent')->default(0)->after('free');
            $table->unsignedInteger('discount_amount')->default(0)->after('discount_percent');

            $table->tinyInteger('tax_percent')->default(0)->after('discount_amount');
            $table->unsignedInteger('tax_amount')->default(0)->after('tax_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['discount_percent', 'discount_amount', 'tax_percent', 'tax_amount']);
        });
    }
};
