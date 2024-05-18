<?php

namespace App\Models;

use App\Models\Area;
use App\Models\User;
use App\Models\Office;
use App\Models\Status;
use App\Models\Division;
use App\Models\Location;
use App\Models\TypeUser;
use App\Models\Department;
use App\Models\TypeActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = 
    [
            'id',
            'code',
            'status_id',
            'area_id',
            'user_id',
            'type_activity_id',
            'title',
            'today_date',
            'start_date',
            'end_date',
            'location_id',
            'office_id',
            'division_id',
            'department_id',
            'progress',
            'observation',
            'search',
    ];


    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
    public static function generateNewCode()
    {
        $code = self::orderBy('code','desc')->first();
        
        if(!isset($code->code))
            return 1;

        return $code->code + 1;
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function typeActivity()
    {
        return $this->belongsTo(TypeActivity::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
        
    }

}
