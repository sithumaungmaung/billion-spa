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
            ['name' => 'Thida', 'phone' => '123456789','price' => 5000, 'branch_id' => Branch::all()->random()->id],
            ['name' => 'Sandar', 'phone' => '123456789','price' => 5000, 'branch_id' => Branch::all()->random()->id],
            ['name' => 'Thaw tar', 'phone' => '123456789','price' => 5000, 'branch_id' => Branch::all()->random()->id],
            ['name' => 'La Pyae', 'phone' => '123456789','price' => 5000, 'branch_id' => Branch::all()->random()->id],
            ['name' => 'Yamin', 'phone' => '123456789','price' => 5000, 'branch_id' => Branch::all()->random()->id],
        ];
    }
}