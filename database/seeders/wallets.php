<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class wallets extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wallets')->insert([
            [
                'user_id' => 1,
                'balance' => 1000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        /*    [
                'user_id' => 2,
                'balance' => 500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'balance' => 750.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'balance' => 250.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'balance' => 1500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ] */
        ]);
    }
}
