<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        for ($i= 1; $i < 10; $i++) {
             Room::create(
                [
                    'name' => 'Room '.$i,
                    'description' => Str::random(5),
                    'branch_id' => Branch::all()->random()->id,
                    'floor' => rand(1,3),
                    'room_number' => Str::random(3), // unique num 1-100 if branch is same
                    'price' => rand(1000, 5000),
                ]
             );
        }

    }
}