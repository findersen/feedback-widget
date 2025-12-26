<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'tickets.view',
            'tickets.update',
            'tickets.statistics',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $admin   = Role::firstOrCreate(['name' => 'admin']);

        $manager->syncPermissions($permissions);
        $admin->syncPermissions($permissions);
    }
}
