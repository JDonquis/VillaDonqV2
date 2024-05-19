<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Models\MainConfig;
use App\Models\SchoolLapse;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateBalance
{

    private $months = [
        'august' => 0,
        'september' => 0,
        'october' => 0,
        'november' => 0,
        'december' => 0,
        'january' => 0,
        'february' => 0,
        'march' => 0,
        'april' => 0,
        'may' => 0,
        'june' => 0,
        'july' => 0,
    ];

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $student = $event->student;
        
        $configData = MainConfig::select('new_inscription_price', 'monthly_payment')->first();
        $schoolLapseActive = SchoolLapse::where('status',1)->first();

        $currentDate = Carbon::now();
        $currentMonthName = strtolower($currentDate->englishMonth);
        $setValue = false;

        foreach ($this->months as $monthName => $value) 
        {
            if($monthName == $currentMonthName && $monthName !== 'august')
                $setValue = true;

            if($setValue)
            {
                $this->months[$monthName] = $configData->monthly_payment;
            }
        }

        DB::table('balance_students')->insert(
            [
                'student_id' => $student->id,
                'school_lapse_id' => $schoolLapseActive->id,
                'inscription' => $configData->new_inscription_price,
                'august' => $this->months['august'],
                'september' => $this->months['september'],
                'october' => $this->months['october'],
                'november' => $this->months['november'],
                'december' => $this->months['december'],
                'january' => $this->months['january'],
                'february' => $this->months['february'],
                'march' => $this->months['march'],
                'april' => $this->months['april'],
                'may' => $this->months['may'],
                'june' => $this->months['june'],
                'july' => $this->months['july'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                
            ]
            );


    }


}
