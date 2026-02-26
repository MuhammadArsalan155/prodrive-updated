<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Instructor extends Authenticatable
{
    use HasFactory,Notifiable,HasRolesAndPermissions;

    protected $table = 'instructors';
    protected $fillable = ['instructor_name', 'email', 'contact', 'password', 'license_number', 'is_active', 'email_verified_at'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }
     /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
    public function roles()
    {
        return $this->morphToMany(
            Role::class, 
            'model', 
            'model_has_roles', 
            'model_id', 
            'role_id'
        )->where('model_type', static::class);
    }

    /**
     * Check if the user has a specific role
     * 
     * @param string|array $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        if (is_array($roleName)) {
            return $this->roles()->whereIn('name', $roleName)->exists();
        }
        
        return $this->roles()->where('name', $roleName)->exists();
    }
}
