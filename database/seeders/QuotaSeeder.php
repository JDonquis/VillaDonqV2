<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB;

class QuotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
             $fields = [

            ['assigned' => 100, 'accepted' => 0, 'remaining' => 100, 'course_id' =>'1','school_lapse_id' => 1 ],
            ['assigned' => 100, 'accepted' => 0, 'remaining' => 100, 'course_id' =>'2','school_lapse_id' => 1 ],
            ['assigned' => 100, 'accepted' => 0, 'remaining' => 100, 'course_id' =>'3','school_lapse_id' => 1 ],
            ['assigned' => 100, 'accepted' => 0, 'remaining' => 100, 'course_id' =>'4','school_lapse_id' => 1 ],
            ['assigned' => 100, 'accepted' => 0, 'remaining' => 100, 'course_id' =>'5','school_lapse_id' => 1 ],
     

         ];   

         DB::table('quotas')->insert($fields);
    }
}
