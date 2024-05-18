<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert
        (
            [
                [
                    'area_id' => 1,
                     'password' => Hash::make('1234'),
                     'name' => 'Juan',
                     'last_name' => 'Donquis',
                     'ci' => '1234',
                     'charge' => 'Desarrollador',
                     'type_user_id' => '1',
                     'search' => 'Juan Donquis 1234 Desarrollador Usuario Sistemas'
                ],
        
            ]
        );
    }
}
