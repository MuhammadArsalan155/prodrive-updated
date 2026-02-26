<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 
        'display_name', 
        'description', 
        'is_system_role'
    ];

    /**
     * Permissions associated with this role
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class, 
            'role_permissions', 
            'role_id', 
            'permission_id'
        );
    }

    /**
     * Models with this role (polymorphic)
     */
    public function models()
    {
        return $this->morphedByMany(
            Model::class, 
            'model', 
            'model_has_roles', 
            'role_id', 
            'model_id'
        );
    }

    /**
     * Sync permissions for a role
     */
    public function syncPermissions(array $permissionIds)
    {
        return $this->permissions()->sync($permissionIds);
    }

    /**
     * Get roles by group
     */
    public static function getRolesByGroup($group = null)
    {
        $query = self::query();
        
        if ($group) {
            $query->where('group', $group);
        }
        return $query->get();
    }
}
