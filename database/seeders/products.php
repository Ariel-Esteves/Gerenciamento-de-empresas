<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class products extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'iPhone 15',
                'description' => 'Latest Apple smartphone with advanced features',
                //'sku' => 'IPH15-001',
                'category_id' => 1, // Electronics
                'brand_id' => 1, // Apple
                'created_at' => now(),
                'updated_at' => now(),
                'price' => 0.00,
                //'product_images_id' => 1,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'description' => 'Premium Android smartphone',
                //'sku' => 'SGS24-001',
                'category_id' => 1, // Electronics
                'brand_id' => 2, // Samsung
                'created_at' => now(),
                'updated_at' => now(),
                'price' => 0.00,
                //'product_images_id' => 2,
            ],
            [
                'name' => 'Nike Air Max 90',
                'description' => 'Classic running shoes',
                //'sku' => 'NAM90-001',
                'category_id' => 3, // Sports
                'brand_id' => 3, // Nike
                'created_at' => now(),
                'updated_at' => now(),
                'price' => 0.00,
               // 'product_images_id' => 3,
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'description' => 'Performance running shoes',
                //'sku' => 'AUB22-001',
                'category_id' => 3, // Sports
                'brand_id' => 4, // Adidas
                'created_at' => now(),
                'updated_at' => now(),
                'price' => 0.00,
               // 'product_images_id' => 4,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Noise-canceling wireless headphones',
                //'sku' => 'SWH1000-001',
                'category_id' => 1, // Electronics
                'brand_id' => 5, // Sony
                'created_at' => now(),
                'updated_at' => now(),
                'price' => 0.00,
                //'product_images_id' => 5,
            ]
        ]);
    }
}
