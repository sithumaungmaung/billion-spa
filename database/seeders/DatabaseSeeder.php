<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RoomSeeder;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\TherapistSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        User::factory()->create([
            'name' => 'Ko Sithu',
            'email' => 'sithu@gmail.com',
            'password' => Hash::make('admin123'),
        ]);


        $this->call(BranchSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(TherapistSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(TimeSlotSeeder::class);
    }
}
