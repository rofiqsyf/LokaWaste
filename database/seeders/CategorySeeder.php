<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Berat & Nasi', 'slug' => 'makanan-berat', 'icon' => 'rice'],
            ['name' => 'Roti & Kue', 'slug' => 'roti-kue', 'icon' => 'bread'],
            ['name' => 'Sayur & Buah Segar', 'slug' => 'sayur-buah', 'icon' => 'apple'],
            ['name' => 'Makanan Olahan & Sup', 'slug' => 'makanan-olahan', 'icon' => 'soup'],
            ['name' => 'Minuman', 'slug' => 'minuman', 'icon' => 'drink'],
            ['name' => 'Bahan Mentah', 'slug' => 'bahan-mentah', 'icon' => 'groceries'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
