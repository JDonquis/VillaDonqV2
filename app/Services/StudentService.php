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

   
    
    public function getStudentsPerCourse($courseId,$sectionId)
    {
        $courseSectionsIds = null;

        $courseSectionsIds = CourseSection::where('course_id',$courseId)
        ->where('section_id',$sectionId)
        ->get()
        ->pluck('id')
        ->toArray();
    

        $students = Student::whereIn('course_section_id',$courseSectionsIds)
        ->with('representative.user','course_section.course','course_section.section')
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

        $student->load('representative.user','course_section.course','course_section.section');
        
        
        // $this->createDocuments($request,$student->id);
        
        event(new StudentCreated($student));

        return 0;

        
    }

    private function createUser($data)
    {
        $password = $data['rep_ci'];
        
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
        $courseSection = CourseSection::where('course_id',$data['course_id'])->where('section_id',$data['section_id'])->first();
        

        $newStudent = Student::create([
           
            'representative_id' => $representativeId,
            'course_section_id' => $courseSection->id,
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

        


        return $newStudent;
    }

}
