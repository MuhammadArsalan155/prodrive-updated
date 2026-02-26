<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name', 
        'display_name', 
        'description', 
        'group', 
        'is_system_permission'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function canBeDeleted()
    {
        return !$this->is_system_permission;
    }
}