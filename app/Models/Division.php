<?php

namespace App\Models;

use App\Models\Office;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',
        'office_id',

    ];

    
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
