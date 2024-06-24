<?php

namespace Database\Seeders;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AccountPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = 
        [
            ['payment_method_id' => 1, 'person_name' => null, 'email' => null, 'ci' => null, 'phone_number' => null, 'bank' => null, 'account_number' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],  
            ['payment_method_id' => 2, 'person_name' => null, 'email' => null, 'ci' => '10478463', 'phone_number' => '04146846012', 'bank' => 'Provincial', 'account_number' => null , 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],            
            ['payment_method_id' => 2, 'person_name' => null, 'email' => null, 'ci' => '10478463', 'phone_number' => '04146846012', 'bank' => 'BNC', 'account_number' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['payment_method_id' => 3, 'person_name' => 'COLEGIO MAESTRO JOSÉ MARTÍ', 'email' => null, 'ci' => '10478463', 'phone_number' => '04146846012', 'bank' => 'BNC', 'account_number' => '0191-0122-34-2100102346', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            
         ];
         
         DB::table('account_payments')->insert($rows);
    }
}
