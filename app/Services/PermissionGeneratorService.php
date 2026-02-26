<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PermissionGeneratorService
{
    /**
     * Generate permissions from routes
     * 
     * @return array Generated permissions
     */
    public function generatePermissionsFromRoutes()
    {
        $routes = Route::getRoutes();
        $dynamicPermissions = [];

        foreach ($routes as $route) {
            // Only process named routes
            if (!$routeName = $route->getName()) {
                continue;
            }

            // Skip specific routes
            if ($this->shouldSkipRoute($routeName)) {
                continue;
            }

            // Generate standardized permission name
            $permissionName = $this->generatePermissionName($routeName);

            // Generate display name
            $displayName = $this->generateDisplayName($routeName);

            // Store unique permission
            $dynamicPermissions[$permissionName] = [
                'name' => $permissionName,
                'display_name' => $displayName,
                'description' => "Access to route: {$routeName}",
                'is_system_permission' => false
            ];
        }

        // Bulk create or update permissions
        return $this->bulkCreatePermissions($dynamicPermissions);
    }

    /**
     * Generate a standardized permission name
     */
    protected function generatePermissionName($routeName)
    {
        // Remove any underscores, convert to kebab-case
        $cleanName = Str::kebab(
            // Remove admin. prefix if exists
            preg_replace('/^admin\./', '', $routeName)
        );

        // Prefix with 'access-'
        return 'access-' . $cleanName;
    }

    /**
     * Generate a human-readable display name
     */
    protected function generateDisplayName($routeName)
    {
        // Remove admin. prefix if exists
        $cleanName = preg_replace('/^admin\./', '', $routeName);
        
        // Convert to title case, replace - and _ with spaces
        return 'Access ' . ucwords(str_replace(['-', '_'], ' ', $cleanName));
    }

    /**
     * Determine if a route should be skipped
     */
    protected function shouldSkipRoute($routeName)
    {
        $skipRoutes = [
            'login', 'logout', 'password.reset', 'home', 
            'register', 'password.request', 'password.email', 
            'password.update', 'password.confirm',
            'ignition', 'telescope'
        ];

        foreach ($skipRoutes as $skipRoute) {
            if (strpos($routeName, $skipRoute) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Bulk create or update permissions
     */
    protected function bulkCreatePermissions($permissions)
    {
        $createdPermissions = [];

        foreach ($permissions as $permissionData) {
            $permission = Permission::updateOrCreate(
                ['name' => $permissionData['name']],
                [
                    'display_name' => $permissionData['display_name'],
                    'description' => $permissionData['description'],
                    'is_system_permission' => $permissionData['is_system_permission']
                ]
            );

            $createdPermissions[] = $permission;
        }

        return $createdPermissions;
    }
}