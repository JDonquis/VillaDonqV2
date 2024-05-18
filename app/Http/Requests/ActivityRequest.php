<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
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
        return [
            
            "type_activity_id" => ['required'],
            "area_id" => ['required'],
            "status_id" => ['required'],
            "title" => ['required'],
            "start_date" => ['required'],
            "end_date" => ['sometimes'],
            "location_id" => ['required'],
            "office_id" => ['required'],
            "division_id" => ['required'],
            "department_id" => ['required'],
            "progress" => ['required'],
            "observation" => ['sometimes'],
        ];
    }
}
