<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function getRoles()
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            $roles = collect([
                ['name' => 'student', 'display_name' => 'Student'],
                ['name' => 'instructor', 'display_name' => 'Instructor'],
                ['name' => 'admin', 'display_name' => 'Admin'],
                ['name' => 'manager', 'display_name' => 'Manager'],
                ['name' => 'parent', 'display_name' => 'Parent']
            ]);
        }

        return response()->json($roles);
    }

    public function validateLogin(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $role = $request->input('role');

        $user = null;
        $valid = false;
        $errorMessage = 'Invalid credentials';

        try {
            $existingRoles = Role::pluck('name')->toArray();
            $validRoles = array_merge(['student', 'instructor', 'admin', 'manager', 'parent'], $existingRoles);

            if (!in_array($role, $validRoles)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Invalid role selected',
                ], 400);
            }

            // Log the validation attempt
            Log::info('Login Validation Attempt', [
                'email' => $email,
                'role' => $role,
                'password_length' => strlen($password)
            ]);

            switch ($role) {
                case 'student':
                    $user = Student::where('email', $email)->first();
                    $passwordField = 'student_password';

                    if ($user) {
                        Log::info('Student Found for Validation', [
                            'student_id' => $user->id,
                            'email' => $user->email,
                            'has_password' => !empty($user->student_password),
                            'password_hash_length' => strlen($user->student_password ?? '')
                        ]);

                        $passwordCheck = Hash::check($password, $user->student_password);
                        Log::info('Student Password Check', [
                            'student_id' => $user->id,
                            'password_check_result' => $passwordCheck ? 'SUCCESS' : 'FAILED',
                            'input_password' => $password,
                            'stored_hash' => $user->student_password
                        ]);

                        if ($passwordCheck) {
                            $valid = true;
                            $errorMessage = '';
                        } else {
                            $errorMessage = 'Incorrect password';
                        }
                    } else {
                        $errorMessage = 'No student found with this email';
                        Log::warning('Student Not Found', ['email' => $email]);
                    }
                    break;

                case 'parent':
                    $user = StudentParent::where('email', $email)->first();
                    $passwordField = 'password';

                    if ($user) {
                        $passwordCheck = Hash::check($password, $user->password);
                        if ($passwordCheck && $user->hasRole('parent')) {
                            $valid = true;
                            $errorMessage = '';
                        } else {
                            $errorMessage = $passwordCheck ? 'Invalid parent credentials' : 'Incorrect password';
                        }
                    } else {
                        $errorMessage = 'No parent found with this email';
                    }
                    break;

                case 'instructor':
                    $user = Instructor::where('email', $email)->first();
                    $passwordField = 'password';

                    if ($user) {
                        $passwordCheck = Hash::check($password, $user->password);
                        if ($passwordCheck) {
                            $valid = true;
                            $errorMessage = '';
                        } else {
                            $errorMessage = 'Incorrect password';
                        }
                    } else {
                        $errorMessage = 'No instructor found with this email';
                    }
                    break;

                case 'admin':
                case 'manager':
                    $user = User::where('email', $email)->first();
                    $passwordField = 'password';

                    if ($user) {
                        $passwordCheck = Hash::check($password, $user->password);
                        if ($passwordCheck && $user->hasRole($role)) {
                            $valid = true;
                            $errorMessage = '';
                        } else {
                            $errorMessage = $passwordCheck ? 'User does not have the selected role' : 'Incorrect password';
                        }
                    } else {
                        $errorMessage = 'No user found with this email and role';
                    }
                    break;

                default:
                    $errorMessage = 'Invalid role specified';
            }

            Log::info('Login Validation Result', [
                'email' => $email,
                'role' => $role,
                'valid' => $valid,
                'error_message' => $errorMessage,
                'user_found' => $user ? 'YES' : 'NO'
            ]);

            return response()->json([
                'valid' => $valid,
                'message' => $errorMessage,
            ]);

        } catch (\Exception $e) {
            Log::error('Login validation error: ' . $e->getMessage(), [
                'email' => $email,
                'role' => $role,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'An error occurred during validation',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password', 'role');

        // Log login attempt
        Log::info('Login Attempt Started', [
            'email' => $credentials['email'],
            'role' => $credentials['role'],
            'ip' => $request->ip()
        ]);

        $availableRoles = Role::pluck('name')->toArray();
        $systemRoles = ['student', 'instructor', 'admin', 'manager', 'parent'];
        $allValidRoles = array_merge($systemRoles, $availableRoles);

        if (!in_array($credentials['role'], $allValidRoles)) {
            Log::warning('Invalid Role Selected', ['role' => $credentials['role']]);
            return back()
                ->withErrors(['role' => 'Invalid role selected'])
                ->withInput($request->only('email', 'role'));
        }

        $loginAttempt = $this->attemptLoginForRole($credentials);

        if ($loginAttempt) {
            $request->session()->regenerate();

            Log::info('Successful Login', [
                'email' => $credentials['email'],
                'role' => $credentials['role'],
                'ip' => $request->ip(),
            ]);

            return $this->redirectAfterLogin($credentials['role']);
        }

        Log::warning('Failed Login Attempt', [
            'email' => $credentials['email'],
            'role' => $credentials['role'],
            'ip' => $request->ip()
        ]);

        return back()
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])
            ->withInput($request->only('email', 'role'));
    }

    protected function attemptLoginForRole($credentials)
    {
        try {
            Log::info('Attempting Login for Role', [
                'email' => $credentials['email'],
                'role' => $credentials['role']
            ]);

            switch ($credentials['role']) {
                case 'student':
                    return $this->studentLogin($credentials);

                case 'parent':
                    return $this->parentLogin($credentials);

                case 'instructor':
                    return $this->instructorLogin($credentials);

                case 'admin':
                case 'manager':
                    return $this->adminManagerLogin($credentials);

                default:
                    return $this->customRoleLogin($credentials);
            }
        } catch (\Exception $e) {
            Log::error('Login Attempt Failed', [
                'email' => $credentials['email'],
                'role' => $credentials['role'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    protected function studentLogin($credentials)
    {
        $student = Student::where('email', $credentials['email'])->first();

        Log::info('Student Login Attempt', [
            'email' => $credentials['email'],
            'student_found' => $student ? 'YES' : 'NO',
            'student_id' => $student ? $student->id : 'N/A'
        ]);

        if ($student) {
            Log::info('Student Details', [
                'student_id' => $student->id,
                'email' => $student->email,
                'has_password' => !empty($student->student_password),
                'password_hash' => $student->student_password,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at
            ]);

            $passwordCheck = Hash::check($credentials['password'], $student->student_password);

            Log::info('Student Password Verification', [
                'student_id' => $student->id,
                'input_password' => $credentials['password'],
                'password_check_result' => $passwordCheck ? 'SUCCESS' : 'FAILED'
            ]);

            if ($passwordCheck) {
                Auth::guard('student')->login($student);
                Log::info('Student Login Successful', [
                    'student_id' => $student->id,
                    'email' => $student->email
                ]);
                return true;
            } else {
                Log::warning('Student Password Mismatch', [
                    'student_id' => $student->id,
                    'email' => $student->email
                ]);
            }
        }

        return false;
    }

    protected function parentLogin($credentials)
    {
        $parent = StudentParent::where('email', $credentials['email'])->first();
        if ($parent && Hash::check($credentials['password'], $parent->password)) {
            if ($parent->hasRole('parent')) {
                Auth::guard('parent')->login($parent);
                return true;
            }
        }
        return false;
    }

    protected function instructorLogin($credentials)
    {
        $instructor = Instructor::where('email', $credentials['email'])->first();

        if ($instructor && Hash::check($credentials['password'], $instructor->password)) {
            if ($instructor->hasRole('instructor')) {
                Auth::guard('instructor')->login($instructor);
                return true;
            }
        }
        return false;
    }

    protected function adminManagerLogin($credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user &&
            Hash::check($credentials['password'], $user->password) &&
            $user->hasRole($credentials['role'])
        ) {
            Auth::login($user);
            return true;
        }
        return false;
    }

    protected function customRoleLogin($credentials)
    {
        $modelTypes = [
            User::class,
            Student::class,
            Instructor::class,
            StudentParent::class
        ];

        foreach ($modelTypes as $modelClass) {
            $guardName = $this->determineGuardName($modelClass);
            $authGuard = Auth::guard($guardName);

            $user = $modelClass::where('email', $credentials['email'])->first();

            if ($user) {
                $passwordField = $this->determinePasswordField($modelClass);

                if (Hash::check($credentials['password'], $user->{$passwordField})) {
                    if ($user->hasRole($credentials['role'])) {
                        $authGuard->login($user);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    protected function determineGuardName($modelClass)
    {
        $guardMap = [
            User::class => 'web',
            Student::class => 'student',
            Instructor::class => 'instructor',
            StudentParent::class => 'parent'
        ];

        return $guardMap[$modelClass] ?? 'web';
    }

    protected function determinePasswordField($modelClass)
    {
        $passwordFieldMap = [
            User::class => 'password',
            Student::class => 'student_password',
            Instructor::class => 'password',
            StudentParent::class => 'password'
        ];

        return $passwordFieldMap[$modelClass] ?? 'password';
    }

    protected function redirectAfterLogin($role)
    {
        $routeMap = [
            'student' => 'student.dashboard',
            'instructor' => 'instructor.dashboard',
            'admin' => 'home',
            'manager' => 'manager.dashboard',
            'parent' => 'parent.dashboard',
        ];
        $routeName = $routeMap[$role] ?? 'dashboard';

        try {
            return redirect()->route($routeName);
        } catch (\Exception $e) {
            Log::warning("Route not found: {$routeName}. Redirecting to default dashboard.", [
                'role' => $role,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('dashboard');
        }
    }

    public function logout(Request $request)
    {
        $guards = ['web', 'student', 'instructor', 'parent'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
