<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HasRolesAndPermissions
{
    /**
     * Polymorphic relationship for roles
     */
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
     * Sync roles for the model with logging
     * 
     * @param array $roleIds Array of role IDs to assign
     * @param string|null $reason Reason for role assignment
     */
    public function syncRoles(array $roleIds, $reason = null)
    {
        // Get current admin/user performing the action
        $adminId = Auth::id() ?? null;

        // Begin transaction for atomic operation
        DB::beginTransaction();

        try {
            // Remove existing roles
            DB::table('model_has_roles')
                ->where('model_type', static::class)
                ->where('model_id', $this->id)
                ->delete();

            // Prepare new roles data
            $rolesData = [];
            $logs = [];
            foreach ($roleIds as $roleId) {
                // Role assignment
                $rolesData[] = [
                    'role_id' => $roleId,
                    'model_id' => $this->id,
                    'model_type' => static::class
                ];

                // Logging
                $logs[] = [
                    'admin_id' => $adminId,
                    'role_id' => $roleId,
                    'model_id' => $this->id,
                    'model_type' => static::class,
                    'action' => 'assign',
                    'reason' => $reason,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Bulk insert new roles
            if (!empty($rolesData)) {
                DB::table('model_has_roles')->insert($rolesData);
            }

            // Log role assignments
            if (!empty($logs)) {
                DB::table('permission_assignment_logs')->insert($logs);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Sync Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the model has a specific role
     */
    public function hasRole($roleName)
    {
        if (is_array($roleName)) {
            return $this->roles()->whereIn('name', $roleName)->exists();
        }
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if the model has a specific permission
     */
    public function hasPermission($permissionName)
    {
        return $this->roles()
            ->whereHas('permissions', function($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();
    }

    /**
     * Get all permissions for the model
     */
    public function getAllPermissions()
    {
        return Permission::whereHas('roles', function($query) {
            $query->whereIn('id', $this->roles->pluck('id'));
        })->get();
    }

    /**
     * Revoke all roles
     */
    public function revokeRoles($reason = null)
    {
        $adminId = Auth::id() ?? null;

        DB::beginTransaction();

        try {
            // Log role revocation
            $currentRoles = $this->roles;
            $logs = $currentRoles->map(function($role) use ($adminId, $reason) {
                return [
                    'admin_id' => $adminId,
                    'role_id' => $role->id,
                    'model_id' => $this->id,
                    'model_type' => static::class,
                    'action' => 'revoke',
                    'reason' => $reason,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            // Remove roles
            DB::table('model_has_roles')
                ->where('model_type', static::class)
                ->where('model_id', $this->id)
                ->delete();

            // Log revocation
            if (!empty($logs)) {
                DB::table('permission_assignment_logs')->insert($logs);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Revocation Error: ' . $e->getMessage());
            return false;
        }
    }
}