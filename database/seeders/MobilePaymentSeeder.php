<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class MobilePaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
          ['payment_method_id' => 2, 'ci' => '10478463', 'phone' => '04146846012', 'bank' => 'Provincial' ],
          ['payment_method_id' => 2, 'ci' => '10478463', 'phone' => '04146846012', 'bank' => 'BNC' ],  
          
        ];

        DB::table('mobile_payments')->insert($rows);
    }
}
