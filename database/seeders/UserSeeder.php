<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Mr. Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin@123'),
            'is_admin' => true,
        ]);

    }
}
