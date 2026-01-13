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
        $staffs = [
            ['name' => 'Therapist 1', 'phone' => '123456789', 'branch_id' => Branch::all()->random()->id],
            ['name' => 'Therapist 2', 'phone' => '123456789', 'branch_id' => Branch::all()->random()->id],
            ['name' => 'Therapist 3', 'phone' => '123456789', 'branch_id' => Branch::all()->random()->id],
        ];

        foreach ($staffs as $staff) {
            Therapist::create($staff);
        }

    }
}
