<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {   
        $courses = Course::all();

        // $course_sections = new CourseSectionCollection(CourseSection::with('section','course')->get());
        
        // $studentsPerCourse = $this->studentService->getStudentsPerCourse(1);
        
        return inertia('Dashboard/Matricula',
        [
            'data' =>
            [
                'courses' => $courses,
            ]
            
        ]);

        // return Inertia('workspace.admin.matricula',['students' => $studentsPerCourse, 'courses' => $courses, 'course_sections' => $course_sections]);

    }
}
