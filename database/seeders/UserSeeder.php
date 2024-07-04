<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   

        

            DB::table('users')->insert([

                'type_user_id' => 1,
                'ci' => '12345678',
                'name' => 'Director',
                'last_name' => 'Perez',
                'email' => 'perez@gmail.com',
                'password' => Hash::make('12345678'),
                'phone_number' => '04125800610',
                'address' => 'Urb. Juan Crisostomo Falcon',
                'date_birth' => '2022-07-09',
                'state' => 'Falcon',
                'city' => 'Coro',
                'photo' => '',

            ]);
    }
}
