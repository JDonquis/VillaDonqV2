<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'status',
        'inscription',
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december',
        'school_lapse_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolLapse()
    {
        return $this->belongsTo(SchoolLapse::class);
    }
}
