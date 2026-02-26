<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;

class DynamicRolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Remove this line - it's stopping execution
        // dd($request);
        
        // Skip authentication for certain routes
        $skipRoutes = [
            'login', 
            'register', 
            'logout', 
            'password.request', 
            'password.email', 
            'password.reset',
            'home',
            'dashboard'
        ];

        $currentRouteName = Route::currentRouteName();
        
        // Skip authentication for specified routes
        if (in_array($currentRouteName, $skipRoutes)) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->handleUnauthenticated($request);
        }

        // Determine the correct user model based on active guards
        $user = $this->getCurrentUser();

        // If no user found, handle as unauthenticated
        if (!$user) {
            return $this->handleUnauthenticated($request);
        }

        // Check role using a more explicit method
        $isAdmin = $this->checkAdminRole($user);

        // Admin always has full access
        if ($isAdmin) {
            return $next($request);
        }

        // Check if the route is admin only
        // if ($this->isAdminOnlyRoute($currentRouteName)) {
        //    return $this->handleUnauthorized($request, $currentRouteName);
        // }

        // Modified permission check to properly handle routes
        $routePermission = 'access-' . $currentRouteName;
        
        // Check if user has permission for this route by name
        if (!$this->checkUserRoutePermission($user, $routePermission)) {
            Log::warning('Permission denied for route', [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'route' => $currentRouteName,
                'permission_needed' => $routePermission
            ]);
            return $this->handleUnauthorized($request, $currentRouteName);
        }

        return $next($request);
    }

    /**
     * Explicitly check if user has admin role
     */
    protected function checkAdminRole($user)
    {
        // Multiple ways to check admin role
        if (method_exists($user, 'roles')) {
            return $user->roles()->where('name', 'admin')->exists();
        }

        // Fallback method if roles() method is not available
        try {
            return $user->hasRole('admin');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Role check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the currently authenticated user across different guards
     */
    protected function getCurrentUser()
    {
        // Check guards in order of priority
        $guards = ['web', 'student', 'instructor'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }

        return null;
    }

    /**
     * Check user permission for the specific route
     */
    protected function checkUserRoutePermission($user, $permissionName)
    {
        // Ensure user exists
        if (!$user) {
            return false;
        }

        try {
            // First try to use hasPermission method if available
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($permissionName);
            }
            
            // Otherwise check permissions through roles
            if (method_exists($user, 'roles')) {
                // Get the user's roles
                $roles = $user->roles;
                
                // For debugging
                Log::info('User roles and permissions check', [
                    'user_id' => $user->id,
                    'user_type' => get_class($user),
                    'roles' => $roles->pluck('name'),
                    'permission_needed' => $permissionName
                ]);
                
                // Check each role for the permission
                foreach ($roles as $role) {
                    if ($role->permissions()->where('name', $permissionName)->exists()) {
                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Permission check failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return false;
    }

    /**
     * Handle unauthenticated requests
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function handleUnauthenticated($request)
    {
        // Log unauthorized access
        Log::warning('Unauthenticated access attempt', [
            'path' => $request->path(),
            'ip' => $request->ip()
        ]);

        // JSON response for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'You must be logged in to access this resource'
            ], 401);
        }

        // Redirect for web requests
        return redirect()->route('login')
            ->withErrors(['message' => 'Please login to access this page']);
    }

    /**
     * Handle unauthorized access
     *
     * @param \Illuminate\Http\Request $request
     * @param string $routeName
     * @return \Illuminate\Http\Response
     */
    protected function handleUnauthorized($request, $routeName)
    {
        // Log unauthorized access
        Log::warning('Unauthorized access attempt', [
            'user_id' => Auth::id(),
            'route' => $routeName,
            'ip' => $request->ip()
        ]);

        // JSON response for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        // Redirect for web requests
        return response()->view('errors.403', [
            'message' => 'You do not have permission to access this page.'
        ], 403);
    }
}