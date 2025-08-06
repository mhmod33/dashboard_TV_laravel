<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    DB::table('periods')->insert([
        ['id'=>1,'plan' => 100, 'duration' => '1 Month', 'period_code' => "P1", 'display_name' => "P1", 'months' => 1, 'days' => 30, 'created_at' => now(), 'updated_at' => now()],
        ['id'=>2,'plan' => 270, 'duration' => '3 Months', 'period_code' => "P2", 'display_name' => "P", 'months' => 1, 'days' => 30, 'created_at' => now(), 'updated_at' => now()],
        ['id'=>3,'plan' => 500, 'duration' => '6 Months', 'period_code' => "P3", 'display_name' => "P1", 'months' => 1, 'days' => 30, 'created_at' => now(), 'updated_at' => now()],
        ['id'=>4,'plan' => 950, 'duration' => '12 Month', 'period_code' => "P4", 'display_name' => "P1", 'months' => 1, 'days' => 30, 'created_at' => now(), 'updated_at' => now()],
        ['id'=>5,'plan' => 0, 'duration' => '1 Month', 'period_code' => "P5", 'display_name' => "P1", 'months' => 1, 'days' => 30, 'created_at' => now(), 'updated_at' => now()],
    ]);    }
}
