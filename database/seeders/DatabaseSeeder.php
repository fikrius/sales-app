<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run() {
        // Seed roles first
        $this->call(RoleSeeder::class);
        
        // Seed permissions
        $this->call(PermissionSeeder::class);
        
        // Create superadmin user
        $superadmin = User::firstOrCreate(
            ['email'=>'superadmin@example.com'],
            ['name'=>'Super Admin','password'=>Hash::make('password')]
        );

        if (!$superadmin->hasRole('superadmin')) {
            $superadmin->assignRole('superadmin');
        }
        
        // Create admin user
        $admin = User::firstOrCreate(
            ['email'=>'admin@example.com'],
            ['name'=>'Admin User','password'=>Hash::make('password')]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
        
        // Create kasir user
        $kasir = User::firstOrCreate(
            ['email'=>'kasir@example.com'],
            ['name'=>'Kasir User','password'=>Hash::make('password')]
        );

        if (!$kasir->hasRole('kasir')) {
            $kasir->assignRole('kasir');
        }
        
        // Create items
        Item::firstOrCreate(['code'=>'ITM-0001'],['name'=>'Produk A','price'=>10000]);
        Item::firstOrCreate(['code'=>'ITM-0002'],['name'=>'Produk B','price'=>20000]);
    }
}
