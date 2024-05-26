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
                    'course_id' =>  $request->input('course') ?? 1,
                    'section_id' => $request->input('section') ?? 1,
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

            return redirect('/dashboard/matricula');

        }
        catch (Exception $e)
        {   
            
            DB::rollback();
             
            return response()->json(['message' => $e->getMessage()],400);
        }
    }

    public function update(CreateStudentRequest $request, $id)
    {
        DB::beginTransaction();

        try 
        {
            $this->studentService->update($request, $id);

            DB::commit();

            return redirect('/dashboard/matricula');

        }
        catch (Exception $e)
        {   
            
            DB::rollback();
             
            return response()->json(['message' => $e->getMessage()],400);
        }
    }
}
