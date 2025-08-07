<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'role' => 'superadmin',
                'balance' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin One',
                'password' => Hash::make('admin1'),
                'status' => 'active',
                'role' => 'admin',
                'balance' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Two',
                'password' => Hash::make('admin2'),
                'status' => 'inactive',
                'role' => 'admin',
                'balance' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Three',
                'password' => Hash::make('admin3'),
                'status' => 'active',
                'role' => 'admin',
                'balance' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Four',
                'password' => Hash::make('admin4'),
                'status' => 'inactive',
                'role' => 'admin',
                'balance' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Subadmin',
                'password' => Hash::make('Subadmin'),
                'status' => 'active',
                'role' => 'subadmin',
                'balance' => 400,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
