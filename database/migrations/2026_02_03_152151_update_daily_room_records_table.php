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
        Schema::table('daily_room_records', function (Blueprint $table) {
            $table->renameColumn('price', 'room_price');

            $table->timestamp('start_time')->nullable()->after('invoice_id');
            $table->timestamp('end_time')->nullable()->after('start_time');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_room_records', function (Blueprint $table) {
            $table->renameColumn('room_price', 'price');

            $table->tinyInteger('time_slot_id')->nullable(false)->change();

            $table->dropColumn('start_time');
            $table->dropColumn('end_time');

        });
    }
};
