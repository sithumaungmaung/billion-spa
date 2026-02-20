<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions

        $permissions = [
            'view-therapists', 'create-therapists', 'update-therapists', 'delete-therapists',

            'view-therapist-types', 'create-therapist-types', 'update-therapist-types', 'delete-therapist-types',

            'view-rooms', 'create-rooms', 'update-rooms', 'delete-rooms',

            'view-daily-room-records', 'create-daily-room-records', 'update-daily-room-records', 'delete-daily-room-records',
        ];

        foreach ($permissions as $key => $permission) {
              Permission::firstOrCreate(['name' => $permission]);
        }



        // $Super_admin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        $admin->givePermissionTo(Permission::all());






    }
}
