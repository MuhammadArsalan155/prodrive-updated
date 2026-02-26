<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class CheckRolePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permissionName
     * @return mixed
     */
    public function handle($request, Closure $next, $permissionName = null)
    {

        // Skip permission check for certain routes or JSON requests
        if ($this->shouldSkipPermissionCheck($request)) {
            return $next($request);
        }

        // Check authentication
        if (!Auth::check()) {
            // For JSON requests, return a JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'You must be logged in to access this resource'
                ], 401);
            }
            
            return redirect('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get current route name
        $currentRouteName = Route::currentRouteName();

        // Admin always has full access
        if ($user->roles()->where('name', 'admin')->exists()) {
            return $next($request);
        }

        // If no specific permission is passed, generate from route name
        if (!$permissionName && $currentRouteName) {
            $permissionName = $this->generatePermissionName($currentRouteName);
        }

        // No permission check needed
        if (!$permissionName) {
            return $next($request);
        }

        // Check if user has the required permission
        $hasPermission = $this->checkUserPermission($user, $permissionName);

        if (!$hasPermission) {
            // Log unauthorized access attempt
            Log::warning("Unauthorized access attempt", [
                'user_id' => $user->id,
                'username' => $user->name,
                'route' => $currentRouteName,
                'required_permission' => $permissionName,
                'ip_address' => $request->ip()
            ]);

            // Handle different response types
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'You do not have permission to access this resource',
                    'required_permission' => $permissionName
                ], 403);
            }

            // Fallback to view for non-JSON requests
            return response()->view('errors.403', [
                'message' => 'You do not have permission to access this page.',
                'required_permission' => $permissionName
            ], 403);
        }

        return $next($request);
    }

    /**
     * Determine if permission check should be skipped
     */
    protected function shouldSkipPermissionCheck($request): bool
    {
        // List of routes or path patterns to skip permission check
        $skipRoutes = [
            'login',
            'login.validate',
            'json-test',
            'password.request',
            'password.email',
            'password.reset'
        ];

        // Check against route name
        $currentRouteName = Route::currentRouteName();
        if (in_array($currentRouteName, $skipRoutes)) {
            return true;
        }

        // Check against path
        $path = $request->path();
        $skipPaths = [
            'login',
            'login/validate',
            'json-test',
            'password/reset',
            'password/email',
            '/',
            ''
        ];
        
        if (in_array($path, $skipPaths)) {
            return true;
        }

        // Skip for JSON test routes or validation routes
        if ($request->is('login/validate') || $request->is('json-test')) {
            return true;
        }

        return false;
    }
    /**
     * Generate permission name from route name
     */
    protected function generatePermissionName($routeName)
    {
        // Convert route name to permission name
        // e.g., 'admin.users.index' -> 'access-admin-users-index'
        return 'access-' . str_replace('.', '-', $routeName);
    }

    /**
     * Check user permission with multiple checking strategies
     */
    protected function checkUserPermission($user, $permissionName)
    {
        // Check exact permission
        $exactPermission = $user->roles()
            ->whereHas('permissions', function($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();

        if ($exactPermission) {
            return true;
        }

        // Check wildcard permissions
        // e.g., 'access-admin-users-*' or 'access-admin-users'
        $wildcardPermissions = [
            // Wildcard at the end
            str_replace('-index', '-*', $permissionName),
            // Parent permission
            preg_replace('/-[^-]*$/', '', $permissionName)
        ];

        $wildcardCheck = $user->roles()
            ->whereHas('permissions', function($query) use ($wildcardPermissions) {
                $query->whereIn('name', $wildcardPermissions);
            })
            ->exists();

        return $wildcardCheck;
    }
}