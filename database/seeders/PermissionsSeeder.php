<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view-staff',
            'view-users',
            'view-companies',
            'view-devices',
            'view-roles',
            'create-user',
            'edit-user',
            'delete-user',
            'restore-user',
            'create-companies',
            'edit-companies',
            'delete-companies',
            'create-devices',
            'edit-devices',
            'delete-devices',
            'create-staff',
            'edit-staff',
            'delete-staff',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $role1 = Role::firstOrCreate(['name' => 'Super Admin']);
        // Super Admin gets all permissions via Gate::before rule usually,
        // but we can also assign them explicitly here if we want.

        $role2 = Role::firstOrCreate(['name' => 'Admin']);
        $role2->syncPermissions($permissions);

        $role3 = Role::firstOrCreate(['name' => 'Manager']);
        $role3->syncPermissions([
            'view-staff',
            'view-users',
            'view-companies',
            'view-devices',
            'create-devices',
            'edit-devices',
            'create-staff',
            'edit-staff',
        ]);
    }
}
