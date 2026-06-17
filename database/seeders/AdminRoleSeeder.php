<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with full access to all panel features']
        );

        Role::firstOrCreate(
            ['name' => 'manager'],
            ['description' => 'Manager role with access to employee management']
        );

        Role::firstOrCreate(
            ['name' => 'employee'],
            ['description'   => 'Standard employee role with limited access']
        );
    }
}
