<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Module;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'area_id',
        'username',
        'name',
        'last_name',
        'ci',
        'charge',
        'type_user_id',
        'password',
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
                $rol = 'user';
                break;
            case 2:
                $rol = 'admin';
                break;
            
            default:
                $rol = 'user';
                break;

        }

        $permissions[] = $rol;

        return $permissions;
    }

}
