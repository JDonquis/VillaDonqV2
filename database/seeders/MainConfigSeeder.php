<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = [

            ['name' => 'VillaDonq',
             'email' => 'villadonq@gmail.com',
             'rif' => 'v123456789',
             'phone_number' => '04125800610',
             'address' => 'Entre Av.Manaure y Av.Ruiz Pineda',
             'release' => '2002-09-07',
             'motto' => 'La escuela del futuro ya llegó a prestarte a mejor educación',
             'regular_inscription_price' => 40,
             'new_inscription_price' => 45,
             'monthly_payment' => 35,
            ]
     

         ];   

         DB::table('main_configs')->insert($fields);
    }
}
