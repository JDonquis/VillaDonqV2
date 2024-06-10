<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'course_id',
        'section_id',
        'name',
        'last_name',
        'date_birth',
        'email',
        'ci',
        'phone_number',
        'sex',
        'previous_school',
        'photo',
        'search',
        'status',
    ];

    public $timestamps = false;

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public static function saveDocs($document, $current = false, $documentName)
    {


            if($current)
            {
                Storage::disk('public')->delete("request/".$documentName."/".$current);
            }

                $doc_name = Str::random(25).".".$document->extension();
            
                $document->storeAs('request/'.$documentName, $doc_name, 'public');
            
                return $doc_name;            
    }
}
