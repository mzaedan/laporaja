<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Lapor Aja',
            'email' => 'admin@gmail.com',
            'password' => bcrypt("admin123")
        ])->assignRole('admin');
    }
}
