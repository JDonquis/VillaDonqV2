<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\CourseSection;
use App\Services\StudentService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\CourseSectionCollection;

class StudentController extends Controller
{   

    public function __construct()
    {
        $this->studentService = new StudentService;
    }

    public function index(Request $request)
    {   
        

        $courses = Course::all();
        $sections = Section::all();

        $course_sections = new CourseSectionCollection(CourseSection::with('section','course')->get());
        $studentsPerCourse = $this->studentService->getStudentsPerCourse($request);

        return inertia('Dashboard/Matricula',
        [
            'data' =>
            [
                'courses' => $courses,
                'sections' => $sections,
                'course_sections' => $course_sections,
                'students' => $studentsPerCourse,
                'filters' => 
                [
                    'course_id' =>  $request->input('course_id') ?? 1,
                    'section_id' => $request->input('section_id') ?? 1,
                    'search' => $request->input('search') ?? null,
                ]
            ]

            
        ]);


    }

    public function store(CreateStudentRequest $request)
    {   
        DB::beginTransaction();

        try 
        {
            $this->studentService->create($request);

            DB::commit();

            return redirect('/dashboard/matricula?course_id='.$request->course_id.'&section_id='.$request->section_id);

        }
        catch (Exception $e)
        {   
            
            DB::rollback();
            
            return redirect('/dashboard/matricula?course_id='.$request->course_id.'&section_id='.$request->section_id)->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function update(UpdateStudentRequest $request, $id)
    {

        DB::beginTransaction();

        try 
        {
            $this->studentService->update($request, $id);

            DB::commit();

            return redirect('/dashboard/matricula?course_id='.$request->course_id.'&section_id='.$request->section_id);

        }
        catch (Exception $e)
        {   
            
            DB::rollback();
             
            return redirect('/dashboard/matricula?course_id='.$request->course_id.'&section_id='.$request->section_id)->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $studentId)
    {   
        $this->studentService->delete($studentId);

        return redirect('/dashboard/matricula');
    }

    public function searchRepresentativeByCI($ci)
    {
        $info = $this->studentService->searchRepresentativeByCI($ci);
        
        return response()->json($info);
    }

    public function searchSecondRepresentativeByCI($ci)
    {
        $info = $this->studentService->searchSecondRepresentativeByCI($ci);
        
        return response()->json($info);
    }

    public function searchRepresentative($search)
    {
        $info = $this->studentService->searchRepresentative($search);
        
        return response()->json($info);
    }

}
