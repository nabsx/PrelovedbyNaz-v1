<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Fashion Wanita', 'description' => 'Pakaian dan aksesoris wanita'],
            ['name' => 'Fashion Pria', 'description' => 'Pakaian dan aksesoris pria'],
            ['name' => 'Tas & Dompet', 'description' => 'Tas dan dompet preloved'],
            ['name' => 'Sepatu', 'description' => 'Sepatu pria dan wanita'],
            ['name' => 'Elektronik', 'description' => 'Gadget dan elektronik bekas'],
            ['name' => 'Kecantikan', 'description' => 'Kosmetik dan skincare'],
            ['name' => 'Rumah Tangga', 'description' => 'Perabotan rumah tangga'],
            ['name' => 'Hobi & Koleksi', 'description' => 'Barang koleksi dan hobi'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => \Illuminate\Support\Str::slug($category['name']),
                'description' => $category['description'],
                'is_active' => true,
            ]);
        }
    }
}