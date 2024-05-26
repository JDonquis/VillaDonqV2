<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Services\StudentService;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{

    public function __construct() 
    {
        $this->studentService = new StudentService;
    }

    public function index()
    {
        //
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $currentlastSectionName_letter = Section::select('name')->orderBy('id','desc')->first()->name;
        $nextLetter = chr(ord($currentlastSectionName_letter) + 1);
        
        $sectionCreated = Section::create(['name' => $nextLetter]);

        DB::table('course_sections')->insert(['course_id' => $request->course_id, 'section_id' => $sectionCreated->id]);

        return redirect('/dashboard/matricula');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {   
        
        $sections = Section::orderBy('id', 'desc')->limit(2)->get();

        

        $currentlastSectionName_letter = Section::select('name')->orderBy('id','desc')->first()->name;
        
        $previousLetter = chr(ord($currentlastSectionName_letter) - 1);

        Section::where('name',$previousLetter)->first();

        DB::table('course_sections')->where('section_id',$id)->delete();
        Section::delete($id);

        return redirect('/dashboard/matricula');

    }
}
