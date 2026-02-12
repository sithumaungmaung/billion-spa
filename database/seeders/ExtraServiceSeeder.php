<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ExtraService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExtraServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['title' => 'Oil massage', 'price' => 30000, 'branch_id' => Branch::all()->random()->id],
            ['title' => 'Face massage', 'price' => 5000, 'branch_id' => Branch::all()->random()->id],
            ['title' => 'Foot massage', 'price' => 10000, 'branch_id' => Branch::all()->random()->id],
            ['title' => 'Body massage', 'price' => 20000, 'branch_id' => Branch::all()->random()->id],
            ['title' => 'Head massage', 'price' => 8000, 'branch_id' => Branch::all()->random()->id],
            ['title' => 'Hand massage', 'price' => 50000, 'branch_id' => Branch::all()->random()->id],

        ];

        foreach ($data as $item) {
            ExtraService::create($item);
        }
    }
}