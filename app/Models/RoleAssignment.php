<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAssignment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'role_id',
        'assignable_type',
        'assignable_id'
    ];

    /**
     * Relationship to the Role model
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Polymorphic relationship to assignable models
     */
    public function assignable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship to the User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to find assignments for a specific role
     */
    public function scopeForRole($query, $roleName)
    {
        return $query->whereHas('role', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Scope to find assignments for a specific model type
     */
    public function scopeForModelType($query, $modelType)
    {
        return $query->where('assignable_type', $modelType);
    }
}