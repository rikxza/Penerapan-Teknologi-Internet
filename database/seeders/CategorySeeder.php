<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kategori Default (untuk semua user, user_id = null)
        $categories = [
            // Kategori Pemasukan (Income)
            ['name' => 'Gaji Bulanan', 'type' => 'income', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pendapatan Sampingan', 'type' => 'income', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],

            // Kategori Pengeluaran (Expense)
            ['name' => 'Makanan & Minuman', 'type' => 'expense', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transportasi', 'type' => 'expense', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tagihan (Listrik, Air, dll)', 'type' => 'expense', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hiburan', 'type' => 'expense', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);
    }
}