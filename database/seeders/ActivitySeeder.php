<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $rows = [];
        for ($i=0; $i < 100 ; $i++) 
        { 
        $rows[] = 
            [
                'code' => $i + 1,
                'status_id' => '2',
                'area_id' => '1',
                'user_id' => '1',
                'type_activity_id' => '1',
                'title' => 'Probando 123',
                'today_date' => '2024-05-16',
                'start_date' => '2024-05-10',
                'end_date' => '2024-05-11',
                'location_id' => '1',
                'office_id' => '7',
                'division_id' => '12',
                'department_id' => '4',
                'progress' => '90',
                'observation' => 'Obselvaxion',
                'search' => NULL,
                'created_at' => '2024-05-16 20:24:14',
                'updated_at' => '2024-05-16 20:24:14'
            ];
            
        }
        DB::table("activities")->insert($rows);
    }
}
