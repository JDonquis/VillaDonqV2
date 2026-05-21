<?php

namespace App\Http\Controllers;

use App\Models\SchoolLapse;
use Inertia\Response;
use Illuminate\Support\Facades\Request;

class AppController
{


    public function index(): Response
    {
        return inertia('Index');
    }

    public function dashboard(): Response
    {
        $schoolLapse = SchoolLapse::get();
        return inertia('Dashboard/Index', [
            'schoolLapses' => $schoolLapse,
        ]);
    }

    public function annualVsMonthlyFlow(Request $request)
    {
        $schoolLapse = null;

        if (!$request->has('schoolId')) {
            $schoolLapse = SchoolLapse::where('status', 1)->first();
        } else {
            $schoolLapse = SchoolLapse::where('id', $request->input('schoolId'))->first();
        }

        return response()->json([
            'annualFlow' => [1000, 1500, 1200, 1800, 2000, 2200, 2500, 2700, 3000, 3200, 3500, 4000],
            'monthlyFlow' => [200, 250, 220, 300, 350, 400, 450, 500, 550, 600, 650, 700],
        ]);
    }

    public function maquinas(): Response
    {
        return inertia('Dashboard/Maquinas');
    }
}
