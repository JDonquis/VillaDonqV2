<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CourseSectionCollection extends ResourceCollection
{

    public function toArray(Request $request)
    {
        $response = [];
        foreach ($this as $courseSection) 
        {   
            $response[$courseSection->course_id][] = ['section_id' => $courseSection->section_id, 'section_name' => $courseSection->section->name];
        }


        return $response;
    }

}
