<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        DB::table('course_sections')->where('section_id',$id)->delete();
        Section::delete($id);

        return redirect('/dashboard/matricula');

    }
}
