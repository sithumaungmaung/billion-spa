<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ExtraServiceSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\TherapistSeeder;
use Database\Seeders\TherapistTypeSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RolePermissionSeeder::class);

        $psn = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
        ]);
        $psn->assignRole('admin');
        // $psn->givePermissionTo('create_post');

        User::factory()->create([
            'name' => 'Ko Sithu',
            'email' => 'sithu@gmail.com',
            'password' => Hash::make('admin123'),
        ]);


        $this->call(BranchSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(TherapistSeeder::class);
        $this->call(TherapistTypeSeeder::class);
        $this->call(ExtraServiceSeeder::class);
        $this->call(ProductSeeder::class);

    }
}