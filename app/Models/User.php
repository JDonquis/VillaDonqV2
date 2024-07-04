<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Module;
use App\Models\Representative;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = 
    [    
        'type_user_id',
        'ci',
        'name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'address',
        'date_birth',
        'state',
        'city',
        'photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'user_modules', 'user_id', 'module_id');
    }

    
    public function representative()
    {
        return $this->hasMany(Representative::class, 'user_id', 'id');
    }

    public function findForCi($ci)
    {
        return self::where('ci',$ci)->with('modules')->first();

    }

    public function getPermissions($user)
    {   
        $permissions = [];

        $modules = $user->modules;

        foreach ($modules as $module) 
        {
            $permissions[] = strval($module->id);       
        }

        $rol = null;
        
        switch ($user->type_user_id) 
        {
            case 1:
                $rol = 'Administrador';
                break;
            case 2:
                $rol = 'Representante';
                break;
            
            case 3:
                $rol = 'Profesor';
                break;

            default:
                $rol = 'Representante';
                break;

        }

        $permissions[] = $rol;

        return $permissions;
    }

}
