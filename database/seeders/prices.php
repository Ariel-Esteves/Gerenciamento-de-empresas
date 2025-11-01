<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class prices extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('price')->insert([
            [
                'product_id' => 1, // iPhone 15
                'price' => 999.99,
                //'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2, // Samsung Galaxy S24
                'price' => 899.99,
                //'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 3, // Nike Air Max 90
                'price' => 129.99,
                //'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 4, // Adidas Ultraboost 22
                'price' => 179.99,
                //'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 5, // Sony WH-1000XM5
                'price' => 399.99,
                //'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
