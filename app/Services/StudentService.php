<?php  

namespace App\Services;

use DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Activity;
use App\Models\CourseSection;
use App\Events\StudentCreated;
use App\Models\Representative;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StudentResource;
use App\Http\Resources\StudentCollection;

class StudentService
{	
	private Student $studentModel;


    public function __construct()
    {
        $this->studentModel = new Student;
    }

   
    
    public function getStudentsPerCourse($request)
    {
        $courseId = $request->input('course') ?? 1;
        $sectionId = $request->input('section') ?? 1;

        $students = Student::query()
        ->when($request->input('search'), function ($query, $search) 
        {
            $query->where('search','like','%' . $search . '%');
        })     
        ->where('course_id',$courseId)
        ->where('section_id',$sectionId)
        ->with('representative.user','course','section')
        ->get();
        

        $studentsCollection = new StudentCollection($students);

        return $studentsCollection;
    }

    public function create($request)
    {   
        $data = $request->all();

        
        $user = User::where('ci',$data['rep_ci'])->first();        
        
        if(!isset($user->id))
            $user = $this->createUser($data);
        
            
        
        $representative = Representative::where('user_id',$user->id)->first();

        if(!isset($representative->id))
            $representative = $this->createRepresentative($data,$user->id);


        $student = $this->createStudent($data,$representative->id);
        

        $student->load('representative.user','course','section');
        
        
        // $this->createDocuments($request,$student->id);
        
        event(new StudentCreated($student));

        return 0;

        
    }

    public function update($request, $studentId)
    {   
        $data = $request->all();

        $user = User::where('id',$data['rep_id'])->first();        
        
        if(!isset($user->id))
            return redirect('/dashboard/matricula')->withErrors(['data' => 'Usuario ID no encontrado']);
        
        
        $user->update([
            'name' => $data['rep_name'],
            'last_name' => $data['rep_last_name'],
            'ci' => $data['rep_ci'],
            'phone_number' => $data['rep_phone_number'],
            'email' => $data['rep_email'] ?? null,
            'password' => Hash::make($data['rep_ci']),
            'address' => $data['address'] ?? null,
            'state' => $data['state'] ?? null,
            'city' => $data['city'] ?? null,
        ]);

        $representative = Representative::where('user_id',$user->id)->first();
        
        if(!isset($representative->id))
            return redirect('/dashboard/matricula')->withErrors(['data' => 'Representante ID no encontrado']);

        
        $representative->update([

            'profession' => $data['rep_profession'] ?? null,
            'workplace' => $data['rep_workplace'] ?? null,
            'second_representative_name' => $data['second_rep_name'] ?? null,
            'second_representative_last_name' => $data['second_rep_last_name'] ?? null,
            'second_representative_ci' => $data['second_rep_ci'] ?? null,
            'second_representative_phone_number' => $data['second_rep_phone_number'] ?? null,
            'second_representative_email' => $data['second_rep_email'] ?? null,
            'second_representative_profession' => $data['second_rep_profession'] ?? null,
            'second_representative_workplace' => $data['second_rep_workplace'] ?? null,
        ]);

        $student = Student::where('id',$studentId)>first();

        if(!isset($student->id))
            return redirect('/dashboard/matricula')->withErrors(['data' => 'Estudiante ID no encontrado']);


        $student->update([
           
            'representative_id' => $representative->id,
            'course_id' => $data['course_id'],
            'section_id' => $data['section_id'],
            'name' => $data['student_name'],
            'last_name' => $data['student_last_name'],
            'date_birth' => $data['student_date_birth'],
            'email' => $data['student_email'] ?? null,
            'ci' => $data['student_ci'] ?? null,
            'phone_number' => $data['student_phone_number'] ?? null,
            'sex' => $data['student_sex'] ?? null,
            'previous_school' => $data['student_previous_school'] ?? null,
        ]);
        

        $student->load('representative.user','course_section.course','course_section.section');
        
        
        // $this->createDocuments($request,$student->id);
        
        event(new StudentCreated($student));

        return 0;

        
    }

    private function createUser($data)
    {
        
        $newUser = User::create([
            'type_user_id' => 2,
            'name' => $data['rep_name'],
            'last_name' => $data['rep_last_name'],
            'ci' => $data['rep_ci'],
            'phone_number' => $data['rep_phone_number'],
            'email' => $data['rep_email'] ?? null,
            'password' => Hash::make($data['rep_ci']),
            'address' => $data['address'] ?? null,
            'state' => $data['state'] ?? null,
            'city' => $data['city'] ?? null,
        ]);
        
        return $newUser;
    }

    private function createRepresentative($data, $userId)
    {
        $newRepresentative = Representative::create([

            'user_id' => $userId,
            'profession' => $data['rep_profession'] ?? null,
            'workplace' => $data['rep_workplace'] ?? null,
            'second_representative_name' => $data['second_rep_name'] ?? null,
            'second_representative_last_name' => $data['second_rep_last_name'] ?? null,
            'second_representative_ci' => $data['second_rep_ci'] ?? null,
            'second_representative_phone_number' => $data['second_rep_phone_number'] ?? null,
            'second_representative_email' => $data['second_rep_email'] ?? null,
            'second_representative_profession' => $data['second_rep_profession'] ?? null,
            'second_representative_workplace' => $data['second_rep_workplace'] ?? null,
        ]);

        return $newRepresentative;
    }

    private function createStudent($data,$representativeId)
    {       
        $newStudent = Student::create([
           
            'representative_id' => $representativeId,
            'course_id' => $data['course_id'],
            'section_id' => $data['section_id'],
            'name' => $data['student_name'],
            'last_name' => $data['student_last_name'],
            'date_birth' => $data['student_date_birth'],
            'email' => $data['student_email'] ?? null,
            'ci' => $data['student_ci'] ?? null,
            'phone_number' => $data['student_phone_number'] ?? null,
            'sex' => $data['student_sex'] ?? null,
            'previous_school' => $data['student_previous_school'] ?? null,
            'photo' => 'guest.webp',
        ]);

        $newStudent->load('representative.user','course','section');

        $search = $this->generateSearch($newStudent);

        $newStudent->update(['search' => $search]);

        


        return $newStudent;
    }

    private function generateSearch($student)
    {

        $search = 
        $student->representative->user->name . ' '
        . $student->representative->user->last_name . ' '
        . $student->course->name . ' '
        . $student->section->name . ' '
        . $student->name . ' '
        . $student->last_name . ' '
        . $student->date_birth . ' '
        . $student->email . ' '
        . $student->ci . ' '
        . $student->phone_number . ' '
        . $student->sex . ' '
        . $student->previous_school . ' ';

        return $search;
    }



}
