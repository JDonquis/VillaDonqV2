<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\StudentService;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{

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

    public function destroy($id)
    {   
        DB::beginTransaction();

        try 
        {
            $sections = Section::orderBy('id', 'desc')->limit(2)->get();
            
            Student::where('course_id',$id)->where('section_id',$sections[0]->id)->update(['section_id',$sections[1]->id]);

            DB::table('course_sections')
            ->where('course_id',$id)
            ->where('section_id',$sections[0]->id)
            ->delete();

            Section::delete($sections[0]->id);
            
            DB::commit();

            return redirect('/dashboard/matricula');
        } 
        catch (Exception $th) 
        {
            DB::rollback();
            return redirect('/dashboard/matricula')->withErrors(['data' => 'Algo ha salido mal :(']);
            
        }


        


    }
}
