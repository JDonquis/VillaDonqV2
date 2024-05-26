<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\CourseSection;
use App\Services\StudentService;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $nextSectionId = $request->section_id + 1;

        CourseSection::create(['course_id' => $request->course_id, 'section_id' => $nextSectionId])->save(); 

        return redirect('/dashboard/matricula');
    }

    // public function destroy($id)
    // {         
    //     CourseSection::where('course_id',$request->course_id), 'section_id' => $nextSectionId])->save(); 


    // }
}
