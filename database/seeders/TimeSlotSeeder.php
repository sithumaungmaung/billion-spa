<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timeSlots = [];
        
        // Generate 24 hours of time slots (00:00 to 23:00)
        for ($hour = 0; $hour < 24; $hour++) {
            // Format hour with leading zero
            $hourFormatted = str_pad($hour, 2, '0', STR_PAD_LEFT);
            
            // Calculate end hour (next hour)
            $endHour = ($hour + 1) % 24;
            $endHourFormatted = str_pad($endHour, 2, '0', STR_PAD_LEFT);
            
            // Create 24-hour format labels
            $slotLabel24h = "{$hourFormatted}:00";
            
            // Create 12-hour format labels with AM/PM
            $hour12 = $hour % 12;
            $hour12 = $hour12 == 0 ? 12 : $hour12; // Convert 0 to 12 for 12-hour format
            $ampm = $hour < 12 ? 'AM' : 'PM';
            $slotLabel12h = "{$hour12}:00 {$ampm}";
            
            $timeSlots[] = [
                'start_time' => "{$hourFormatted}:00:00",
                'end_time' => "{$endHourFormatted}:00:00",
                'slot_label' => $slotLabel24h, // Using 24-hour format as label
                // 'display_label' => $slotLabel12h, // Optional: Add a display label
                'hour_value' => $hour, // Store the hour as integer for easy querying
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Check if data already exists to avoid duplicates
        if (DB::table('time_slots')->count() == 0) {
            DB::table('time_slots')->insert($timeSlots);
            $this->command->info('24-hour time slots seeded successfully!');
            
            // Display summary
            $this->command->info('Created time slots from 00:00 to 23:00');
            $this->command->info('First slot: ' . $timeSlots[0]['start_time'] . ' - ' . $timeSlots[0]['end_time']);
            $this->command->info('Last slot: ' . $timeSlots[23]['start_time'] . ' - ' . $timeSlots[23]['end_time']);
        } else {
            $this->command->info('Time slots already exist. Skipping seeding.');
        }
    }
}
