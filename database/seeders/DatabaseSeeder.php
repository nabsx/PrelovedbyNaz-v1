<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin Preloved',
            'email' => 'admin@prelovedbynaz.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create regular user
        User::create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // You can also seed some products
        \App\Models\Product::create([
            'name' => 'Dress Preloved Cantik',
            'slug' => 'dress-preloved-cantik',
            'description' => 'Dress preloved kondisi masih bagus, bahan nyaman',
            'price' => 85000,
            'stock' => 5,
            'is_active' => true,
        ]);

        \App\Models\Product::create([
            'name' => 'Tas Second Branded',
            'slug' => 'tas-second-branded',
            'description' => 'Tas branded second, masih layak pakai',
            'price' => 150000,
            'stock' => 3,
            'is_active' => true,
        ]);
    }
}