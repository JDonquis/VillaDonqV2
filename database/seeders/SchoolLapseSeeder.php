<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolLapseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {       
        $date = Carbon::yesterday();
        $nextWeek = Carbon::today()->addWeek();

        DB::table('school_lapses')->insert(
            [
            'start' => $date,
            'end' => $nextWeek,
            'status' => 1,
            'created_at' => $date,
            'updated_at' => $date
            ]);
    }
}
