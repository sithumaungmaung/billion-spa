<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $branches = [
            ['name' => 'KyoutTaDar', 'description' => Str::random(5)],
            ['name' => 'ThuWaNa', 'description' => Str::random(3)],
            ['name' => 'ThanLyin', 'description' => Str::random(2)],
            ['name' => 'North Dagon', 'description' => Str::random(2)],
        ];


        foreach ($branches as $branch) {
            Branch::create($branch);
        }

    }
}
