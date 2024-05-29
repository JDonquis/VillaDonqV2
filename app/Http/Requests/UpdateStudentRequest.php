<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $id = request()->route('id');
        return [

            'student_name' => ['required'],
            'student_last_name' => ['required'],
            'student_date_birth' => ['required'],
            'student_email' => ['sometimes'],
            'student_ci' => ['required','unique:students,ci,'.$id],
            'student_phone_number' => ['sometimes'],
            'student_sex' => ['sometimes'],
            'student_previous_school' => ['sometimes'],
            'course_id' => ['required'],
            'section_id' => ['required'],
            'state' => ['sometimes'],
            'city' => ['sometimes'],
            'address' => ['sometimes'],
            'rep_id' => ['sometimes'],
            'rep_name' => ['required'],
            'rep_last_name' => ['required'],
            'rep_ci' => ['required'],
            'rep_phone_number' => ['required'],
            'rep_email' => ['sometimes'],
            'rep_profession' => ['sometimes'],
            'rep_workplace' => ['sometimes'],
            'second_rep_name' => ['sometimes'],
            'second_rep_last_name' => ['sometimes'],
            'second_rep_ci' => ['sometimes'],
            'second_rep_phone_number' => ['sometimes'],
            'second_rep_email' => ['sometimes'],
            'second_rep_profession' => ['sometimes'],
            'second_rep_workplace' => ['sometimes'],
        ];
    }
}
