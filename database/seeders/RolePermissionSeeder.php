<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = ['users', 'roles', 'permissions', 'categories', 'brands', 'attributes', 'products'];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$module}"]);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $manager    = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $staff      = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $userRole   = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // Super Admin gets everything (belt-and-suspenders alongside Gate::before)
        $superAdmin->syncPermissions(Permission::all());

        // Admin: full access to users/roles, view-only on permissions
        $admin->syncPermissions([
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_brands', 'create_brands', 'edit_brands', 'delete_brands',
            'view_attributes', 'create_attributes', 'edit_attributes', 'delete_attributes',
            'view_products', 'create_products', 'edit_products', 'delete_products',
            'view_roles', 'create_roles', 'edit_roles',
            'view_permissions',
        ]);

        // Manager: manage users, view roles/permissions
        $manager->syncPermissions([
            'view_users', 'create_users', 'edit_users',
            'view_roles',
        ]);

        // Staff: view-only
        $staff->syncPermissions([
            'view_users',
        ]);
    }
}