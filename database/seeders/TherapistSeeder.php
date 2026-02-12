<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Therapist;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TherapistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffs = range(1, 10);

        foreach ($staffs as $i => $staff) {
            Therapist::create(
                ['name' => "Therapist $staff", 'phone' => '123456789', 'branch_id' => Branch::all()->random()->id]
            );
        }

    }
}