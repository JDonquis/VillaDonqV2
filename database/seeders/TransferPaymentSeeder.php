<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class TransferPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['payment_method_id' => 3, 'ci' => '10478463', 'account_number' => '0191-0122-34-2100102346', 'bank' => 'BNC CUENTA CORRIENTE', 'name' => 'Colegio Maestro José Martí']
        ];

        DB::table('transfer_payments')->insert($rows);
    }
}
