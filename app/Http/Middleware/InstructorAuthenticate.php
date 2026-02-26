<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructorAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // Extensive logging for debugging
        Log::info('Instructor Authentication Middleware', [
            'path' => $request->path(),
            'method' => $request->method(),
            'is_instructor_authenticated' => Auth::guard('instructor')->check(),
            'authenticated_user' => Auth::guard('instructor')->user()
        ]);

        // Check if user is authenticated via instructor guard
        if (!Auth::guard('instructor')->check()) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized Instructor Access Attempt', [
                'path' => $request->path(),
                'ip' => $request->ip()
            ]);

            // For JSON requests
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Please log in as an instructor'
                ], 401);
            }

            // Redirect to login for web requests
            return redirect()->route('login')
                ->withErrors(['error' => 'Please log in as an instructor']);
        }

        return $next($request);
    }
}