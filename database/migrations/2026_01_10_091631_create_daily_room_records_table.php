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
            $table->foreignId('room_id')->constrained('rooms', 'room_id')->onDelete('cascade');
            $table->tinyInteger('time_slot_id');
            $table->foreignId('therapist_id')->nullable()->constrained('therapists', 'therapist_id')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->tinyInteger('service_type')->default(0); // 0 for Normal, 1 for By Name
            $table->timestamps();
            
            // Composite unique constraint to prevent duplicate entries
            $table->unique(['record_date', 'room_id', 'time_slot_id'], 'unique_record');
            
            // Indexes for better performance
            $table->index('record_date');
            $table->index(['room_id', 'time_slot_id']);
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