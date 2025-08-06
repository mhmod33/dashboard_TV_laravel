<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payments')->insert([
        [
            'serial_number' => '100000000001',
            'payment_id' => 'PAY0001',
            'owner' => 'Admin One',
            'customer_name' => 'Ahmed Ali',
            'date' => now(),
            'duration' => '1 Month',
            'exp_before' => now()->subMonth(),
            'exp_after' => now()->addMonth(),
            'cost' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'serial_number' => '100000000002',
            'payment_id' => 'PAY0002',
            'owner' => 'Admin Two',
            'customer_name' => 'Mona Hassan',
            'date' => now(),
            'duration' => '3 Months',
            'exp_before' => now()->subMonths(3),
            'exp_after' => now(),
            'cost' => 270,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'serial_number' => '100000000003',
            'payment_id' => 'PAY0003',
            'owner' => 'Admin Three',
            'customer_name' => 'Youssef Mahmoud',
            'date' => now(),
            'duration' => '1 Month',
            'exp_before' => now()->subMonth(),
            'exp_after' => now()->addMonth(),
            'cost' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'serial_number' => '100000000004',
            'payment_id' => 'PAY0004',
            'owner' => 'Admin One',
            'customer_name' => 'Sara Nabil',
            'date' => now(),
            'duration' => '6 Months',
            'exp_before' => now()->subMonths(6),
            'exp_after' => now()->addMonths(6),
            'cost' => 500,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'serial_number' => '100000000005',
            'payment_id' => 'PAY0005',
            'owner' => 'Admin Two',
            'customer_name' => 'Hassan Mohamed',
            'date' => now(),
            'duration' => '12 Month',
            'exp_before' => now(),
            'exp_after' => now()->addYear(),
            'cost' => 950,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }
}
