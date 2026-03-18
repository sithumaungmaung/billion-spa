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
        Schema::create('excel_export_lists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('remark')->nullable();
            $table->string('file_name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('branch_id')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excel_export_lists');
    }
};
