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
           $table->foreignId('branch_id')
                ->nullable()
                ->after('room_id')
                ->constrained('branches')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_room_records', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
    }
};
