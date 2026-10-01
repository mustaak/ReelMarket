<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'users', 'roles', 'permissions', 'categories', 'brands', 'attributes', 'products',
            'posts', 'reels', 'reports', 'orders',
        ];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$module}"]);
            }
        }

        foreach (['manage_user_status', 'view_profile', 'edit_profile'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $staff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // Super Admin gets everything (belt-and-suspenders alongside Gate::before)
        $superAdmin->syncPermissions(Permission::all());

        // Admin: full access to users/roles, view-only on permissions
        $admin->syncPermissions([
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_brands', 'create_brands', 'edit_brands', 'delete_brands',
            'view_attributes', 'create_attributes', 'edit_attributes', 'delete_attributes',
            'view_products', 'create_products', 'edit_products', 'delete_products',
            'view_posts', 'create_posts', 'edit_posts', 'delete_posts',
            'view_reels', 'create_reels', 'edit_reels', 'delete_reels',
            'view_reports', 'create_reports', 'edit_reports', 'delete_reports',
            'view_orders', 'edit_orders', 'manage_user_status',
            'view_profile', 'edit_profile',
            'view_roles', 'create_roles', 'edit_roles',
            'view_permissions',
        ]);

        // Manager: manage users, view roles/permissions
        $manager->syncPermissions([
            'view_users', 'create_users', 'edit_users',
            'view_roles',
            'view_posts', 'view_reels', 'view_reports',
        ]);

        // Staff: view-only
        $staff->syncPermissions([
            'view_users',
        ]);
    }
}
