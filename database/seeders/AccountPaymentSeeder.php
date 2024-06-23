<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class AccountPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [

            ['payment_method_id' => 1, 'specific_id' => 0],
            ['payment_method_id' => 2, 'specific_id' => 1],
            ['payment_method_id' => 2, 'specific_id' => 2],
            ['payment_method_id' => 3, 'specific_id' => 1],

        ];
    }
}
