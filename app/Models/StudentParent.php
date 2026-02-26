<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasRolesAndPermissions;

class StudentParent extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

    protected $table = 'parents';

    protected $fillable = [
        'name',
        'email',
        'password',
        'contact',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}