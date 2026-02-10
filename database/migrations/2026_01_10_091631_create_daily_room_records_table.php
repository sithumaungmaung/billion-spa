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
        Schema::create('daily_room_records', function (Blueprint $table) {
            $table->id();
            $table->date('record_date');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            // $table->tinyInteger('time_slot_id');
            $table->foreignId('therapist_id')->nullable()->constrained('therapists')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedInteger('room_price')->default(0);
             $table->integer('service_type_price')->default(0);
            $table->tinyInteger('service_type')->default(0); // 0 for Normal, 1 for By Name
            $table->integer('invoice_id')->nullable();

            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->timestamps();

            // Composite unique constraint to prevent duplicate entries
            $table->unique(['record_date', 'room_id'], 'unique_record');

            // Indexes for better performance
            $table->index('record_date');
            $table->index(['room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_room_records');
    }
};
