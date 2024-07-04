<?php  

namespace App\Services;

use DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Activity;
use App\Models\CourseSection;
use App\Events\StudentCreated;
use App\Events\StudentUpdated;
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
        $courseId = $request->input('course_id') ?? 1;
        $sectionId = $request->input('section_id') ?? 1;

        $students = Student::query()
        ->where('status','!=',0)
        ->where('course_id',$courseId)
        ->where('section_id',$sectionId)
        ->when($request->input('search'), function ($query, $search) 
        {
            $query->where('search','like','%' . $search . '%');
        })     
      
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

        $representative = Representative::where('id',$data['rep_id'])->first();
        
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

        $user = User::where('id',$representative->user_id)->first();        
        
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

       

        $student = Student::where('id',$studentId)->first();


        if(!isset($student->id))
            return redirect('/dashboard/matricula')->withErrors(['data' => 'Estudiante ID no encontrado']);

        $previousCourseId = $student->course_id ;

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
        
        $student->load('representative.user','course','section');
        
        // $this->createDocuments($request,$student->id);
        
        event(new StudentUpdated($previousCourseId,$student));

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

    public function searchRepresentativeByCI($ci)
    {
        $user = User::where('ci',$ci)->where('type_user_id',2)->first();

        if(!isset($user->id))
            return redirect('/dashboard/matricula')->withErrors(['data' => null]);
        
        $representative = Representative::where('user_id',$user->id)->first();

        if(!isset($representative->id))
            return redirect('/dashboard/matricula')->withErrors(['data' => null]);

        $data = 
        [

            'rep_id' => $representative->id,
            'rep_name' => $user->name,
            'rep_last_name' => $user->last_name,
            'rep_ci' => $user->ci,
            'rep_phone_number' => $user->phone_number,
            'rep_email' => $user->email ?? null,
            'rep_profession' => $representative->profession ?? null,
            'rep_workplace' => $representative->workplace ?? null,

        ];

        return $data;
    }

    public function searchSecondRepresentativeByCI($ci)
    {
        $user = User::where('ci',$ci)->where('type_user_id')->first();

        if(!isset($user->id))
            return response()->json(['data' => null]);
        
            $representative = Representative::where('user_id',$user->id)->first();

        if(!isset($representative->id))
            return response()->json(['data' => null]);
        
        $data = 
        [

            'second_rep_name' => $representative->second_representative_name ?? null,
            'second_rep_last_name' => $representative->second_representative_last_name ?? null,
            'second_rep_ci' => $representative->second_representative_ci ?? null,
            'second_rep_phone_number' => $representative->second_representative_phone_number ?? null,
            'second_rep_email' => $representative->second_representative_email ?? null,
            'second_rep_profession' => $representative->second_representative_profession ?? null,
            'second_rep_workplace' => $representative->second_representative_workplace ?? null,

        ];

        return $data;
    }

    public function searchRepresentative($search)
    {
        $user = User::where('type_user_id', 2)
          ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
          ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%'])
          ->orWhereRaw('LOWER(ci) LIKE ?', ['%' . strtolower($search) . '%'])
          ->with('representative')
          ->get();

        return $user;
    }

    public function delete($studentId)
    {
        $student = Student::find($studentId);
        $student->update(['status' => 0]);

        return 0;
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
