<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\CourseSection;
use App\Http\Resources\CourseSectionCollection;

class StudentController extends Controller
{
    public function index()
    {   
        $courses = Course::all();
        $sections = Section::all();
        $course_sections = new CourseSectionCollection(CourseSection::with('section','course')->get());
        
        // $studentsPerCourse = $this->studentService->getStudentsPerCourse(1);
        // return response()->json(['courses' => $courses, 'sections' => $sections,'course_sections' => $course_sections]);
        return inertia('Dashboard/Matricula',
        [
            'data' =>
            [
                'courses' => $courses,
                'sections' => $sections,
                'course_sections' => $course_sections
            ]
            
        ]);

        // return Inertia('workspace.admin.matricula',['students' => $studentsPerCourse, 'courses' => $courses, 'course_sections' => $course_sections]);

    }
}
