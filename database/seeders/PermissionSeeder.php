<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Sales permissions
            'sale-create',
            'sale-read',
            'sale-update',
            'sale-delete',
            
            // Payment permissions
            'payment-create',
            'payment-read',
            'payment-update',
            'payment-delete',
            
            // User permissions
            'user-create',
            'user-read',
            'user-update',
            'user-delete',
            
            // Item permissions
            'item-create',
            'item-read',
            'item-update',
            'item-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $superadmin = Role::findByName('superadmin');
        $superadmin->givePermissionTo([
            'sale-create', 'sale-read', 'sale-update', 'sale-delete',
            'payment-create', 'payment-read', 'payment-update', 'payment-delete',
            'user-create', 'user-read', 'user-update', 'user-delete',
            'item-create', 'item-read', 'item-update', 'item-delete',
        ]);

        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'sale-create', 'sale-read', 'sale-update', 'sale-delete',
            'payment-create', 'payment-read', 'payment-update', 'payment-delete',
            'user-create', 'user-read', 'user-update',
            'item-create', 'item-read', 'item-update',
        ]);
    }
}
