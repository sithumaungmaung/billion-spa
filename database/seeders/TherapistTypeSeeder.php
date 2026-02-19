<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\TherapistType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TherapistTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['title' => 'Normal', 'price' => 0, 'branch_id' => Branch::all()->random()->id],
            ['title' => 'VIP', 'price' => 5000, 'branch_id' => Branch::all()->random()->id],
            ['title' => 'VVIP', 'price' => 10000, 'branch_id' => Branch::all()->random()->id],
            ['title' => 'Elite', 'price' => 20000, 'branch_id' => Branch::all()->random()->id],
        ];

        foreach ($data as $item) {
            TherapistType::create($item);
        }
    }
}
