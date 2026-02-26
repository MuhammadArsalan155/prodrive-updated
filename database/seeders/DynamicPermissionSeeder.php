<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class DynamicPermissionSeeder extends Seeder
{
    /**
     * List of routes to exclude from permission generation
     * @var array
     */
    protected $excludedRoutes = ['login', 'registerPage', 'register', 'payment.success', 'payment.cancel', 'logout', 'password.reset', 'password.request', 'password.email', 'password.update', 'home', 'register', 'verification.notice', 'verification.verify', 'verification.send', 'ignition', 'telescope', 'generated::', ''];

    /**
     * Prefix mappings for role-based route permissions
     * @var array
     */
    protected $rolePrefixMappings = [
        'admin' => 'access-admin-',
        'manager' => 'access-admin-|access-manager-',
        'instructor' => 'access-instructor-',
        'student' => 'access-student-',
        'parent' => 'access-parent-',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Generate permissions from routes
        $dynamicPermissions = $this->generatePermissionsFromRoutes();

        // Create or update permissions
        $this->processPermissions($dynamicPermissions);

        // Assign dynamic permissions to roles
        $this->assignDynamicPermissionsToRoles();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Dynamic permission seeding completed.');
    }

    /**
     * Generate permissions based on named routes
     * @return array
     */
    protected function generatePermissionsFromRoutes()
    {
        $routes = Route::getRoutes();
        $dynamicPermissions = [];
        $processedRouteNames = [];

        foreach ($routes as $route) {
            // Only process named routes
            $routeName = $route->getName();

            if (!$routeName) {
                continue;
            }

            // Skip routes with generated:: prefix or in excluded routes
            if (strpos($routeName, 'generated::') !== false || in_array($routeName, $this->excludedRoutes) || in_array($routeName, $processedRouteNames)) {
                continue;
            }

            // Generate permission details
            $permissionName = $this->generatePermissionName($routeName);
            $displayName = $this->generateDisplayName($routeName);
            $description = "Access to route: {$routeName}";

            // Store unique permission
            $dynamicPermissions[] = [
                'name' => $permissionName,
                'display_name' => $displayName,
                'description' => $description,
                'is_system_permission' => false,
                'route_name' => $routeName,
            ];

            $processedRouteNames[] = $routeName;

            $this->command->info("Dynamic permission '{$permissionName}' generated from route '{$routeName}'");
        }

        return $dynamicPermissions;
    }
    /**
     * Process and store permissions
     * @param array $dynamicPermissions
     */
    protected function processPermissions(array $dynamicPermissions)
    {
        // Collect permission names for cleanup
        $permissionNames = [];

        foreach ($dynamicPermissions as $permissionData) {
            // Try to find an existing permission
            $existingPermission = Permission::where('name', $permissionData['name'])->first();

            if ($existingPermission) {
                // Check for updates
                $hasChanges = false;

                if ($existingPermission->display_name !== $permissionData['display_name']) {
                    $existingPermission->display_name = $permissionData['display_name'];
                    $hasChanges = true;
                }

                if ($existingPermission->description !== $permissionData['description']) {
                    $existingPermission->description = $permissionData['description'];
                    $hasChanges = true;
                }

                // Save changes if any
                if ($hasChanges) {
                    $existingPermission->save();
                    $this->command->info("Permission {$permissionData['name']} updated.");
                }
            } else {
                // Create new permission
                Permission::create([
                    'name' => $permissionData['name'],
                    'display_name' => $permissionData['display_name'],
                    'description' => $permissionData['description'],
                    'is_system_permission' => $permissionData['is_system_permission'],
                ]);
                $this->command->info("Permission {$permissionData['name']} created.");
            }

            $permissionNames[] = $permissionData['name'];
        }

        // Remove obsolete permissions
        Permission::where('is_system_permission', false)->whereNotIn('name', $permissionNames)->delete();
    }

    /**
     * Generate a standardized permission name from route name
     * @param string $routeName
     * @return string
     */
    protected function generatePermissionName($routeName)
    {
        // Convert route name to permission name
        // (e.g. 'student.dashboard' -> 'access-student-dashboard')
        return 'access-' . str_replace('.', '-', $routeName);
    }

    /**
     * Generate a human-readable display name
     * @param string $routeName
     * @return string
     */
    protected function generateDisplayName($routeName)
    {
        // Convert route name to a readable format
        // (e.g. 'student.dashboard' -> 'Access Student Dashboard')
        $parts = explode('.', $routeName);
        $displayName = implode(' ', array_map('ucfirst', $parts));
        return "Access {$displayName}";
    }

    /**
     * Assign dynamic permissions to roles
     */
    protected function assignDynamicPermissionsToRoles()
    {
        // Retrieve roles
        $roles = Role::whereIn('name', array_keys($this->rolePrefixMappings))->get();

        foreach ($roles as $role) {
            // Get permission prefixes for this role
            $prefixes = explode('|', $this->rolePrefixMappings[$role->name]);

            // Collect permissions matching any of the prefixes
            $permissions = Permission::where(function ($query) use ($prefixes) {
                foreach ($prefixes as $prefix) {
                    $query->orWhere('name', 'like', $prefix . '%');
                }
            })->get();

            // Sync permissions without creating duplicates
            if ($permissions->isNotEmpty()) {
                $role->permissions()->syncWithoutDetaching($permissions->pluck('id')->toArray());
                $this->command->info("Assigned {$permissions->count()} dynamic permissions to {$role->name} role");
            }
        }

        // Admin role gets all permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->syncWithoutDetaching($allPermissions->pluck('id')->toArray());
            $this->command->info('Assigned all permissions to admin role');
        }
    }
}
