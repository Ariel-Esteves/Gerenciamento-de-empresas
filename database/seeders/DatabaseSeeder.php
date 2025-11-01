<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Populate all tables in correct order (respecting foreign key dependencies)
        $this->call([
            EmpresaSeeder::class,
            brands::class,           // Must run before products
            categories::class,       // Must run before products
            products::class,         // Must run before images and prices
            images::class,           // Depends on products
            prices::class,           // Depends on products
            StockMovementSeeder::class, // Must run before stock
            StockSeeder::class,      // Depends on products and stock movements
            WalletSeeder::class,     // Depends on users
            WalletTransactionSeeder::class, // Depends on wallets
            wallets::class,          // Depends on users
        ]);
    }
}
