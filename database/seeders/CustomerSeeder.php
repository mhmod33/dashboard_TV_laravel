<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers')->insert([
        [
            'serial_number' => '100000000001',
            'customer_name' => 'Ahmed Ali',
            'address' => 'Cairo',
            'phone' => '01012345678',
            'plan_id' => 1,
            'payment_status' => 'paid',
            'status' => 'active',
            'admin_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'serial_number' => '100000000002',
            'customer_name' => 'Mona Hassan',
            'address' => 'Alexandria',
            'phone' => '01098765432',
            'plan_id' => 2,
            'payment_status' => 'unpaid',
            'status' => 'expired',
            'admin_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'serial_number' => '100000000003',
            'customer_name' => 'Youssef Mahmoud',
            'address' => 'Giza',
            'phone' => '01111222333',
            'plan_id' => 1,
            'payment_status' => 'paid',
            'status' => 'active',
            'admin_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'serial_number' => '100000000004',
            'customer_name' => 'Sara Nabil',
            'address' => 'Mansoura',
            'phone' => '01233445566',
            'plan_id' => 3,
            'payment_status' => 'paid',
            'status' => 'expired',
            'admin_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'serial_number' => '100000000005',
            'customer_name' => 'Hassan Mohamed',
            'address' => 'Tanta',
            'phone' => '01000000000',
            'plan_id' => 4,
            'payment_status' => 'unpaid',
            'status' => 'active',
            'admin_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ],
    ]);
    }
}
