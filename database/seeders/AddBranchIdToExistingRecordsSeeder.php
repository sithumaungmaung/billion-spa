<?php

namespace Database\Seeders;

use App\Models\DailyRoomRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddBranchIdToExistingRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = DailyRoomRecord::all();
        foreach ($records as $record) {
            if($record->branch_id !== null) {
                continue;
            }
            $record->branch_id = 1;
            $record->save();
        }
    }
}