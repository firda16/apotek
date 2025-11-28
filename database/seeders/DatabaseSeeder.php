<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user admin
    User::factory()->create([
        'name' => 'admin',
        'email' => 'admin@admin.com',
        'password' => bcrypt('admin'),
        'role' => 'admin',
    ]);

        // Buat user kasir
        User::factory()->create([
            'name' => 'kasir',
            'email' => 'kasir@kasir.com',
            'password' => bcrypt('kasir'),
            'role' => 'kasir', 
        ]);
    }
}
