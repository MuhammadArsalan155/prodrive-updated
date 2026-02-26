<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\User;
use App\Models\Role;
use App\Models\PasswordReset; // You'll need to create this model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
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

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|string'
        ]);

        $email = $request->input('email');
        $role = $request->input('role');

        // Find user across all tables based on role
        $userInfo = $this->findUserByEmailAndRole($email, $role);

        if (!$userInfo) {
            return back()->withErrors([
                'email' => 'We could not find a user with that email address and role combination.'
            ]);
        }

        // Generate reset token
        $token = Str::random(64);

        // Store reset token in database
        PasswordReset::updateOrCreate(
            [
                'email' => $email,
                'role' => $role
            ],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Send reset email
        $this->sendResetEmail($userInfo, $token);

        return back()->with([
            'status' => 'We have emailed your password reset link!'
        ]);
    }

    protected function findUserByEmailAndRole($email, $role)
    {
        $user = null;
        $userType = null;
        $passwordField = 'password';

        switch ($role) {
            case 'student':
                $user = Student::where('email', $email)->first();
                if ($user && $user->hasRole('student')) {
                    $userType = 'student';
                    $passwordField = 'student_password';
                } else {
                    $user = null;
                }
                break;

            case 'parent':
                $user = StudentParent::where('email', $email)->first();
                if ($user && $user->hasRole('parent')) {
                    $userType = 'parent';
                    $passwordField = 'password';
                } else {
                    $user = null;
                }
                break;

            case 'instructor':
                $user = Instructor::where('email', $email)->first();
                if ($user && $user->hasRole('instructor')) {
                    $userType = 'instructor';
                    $passwordField = 'password';
                } else {
                    $user = null;
                }
                break;

            case 'admin':
            case 'manager':
                $user = User::where('email', $email)->first();
                if ($user && $user->hasRole($role)) {
                    $userType = 'user';
                    $passwordField = 'password';
                } else {
                    $user = null;
                }
                break;

            default:
                // Handle custom roles - check across all models
                $modelTypes = [
                    ['model' => User::class, 'type' => 'user', 'password_field' => 'password'],
                    ['model' => Student::class, 'type' => 'student', 'password_field' => 'student_password'],
                    ['model' => Instructor::class, 'type' => 'instructor', 'password_field' => 'password'],
                    ['model' => StudentParent::class, 'type' => 'parent', 'password_field' => 'password']
                ];

                foreach ($modelTypes as $modelInfo) {
                    $tempUser = $modelInfo['model']::where('email', $email)->first();
                    if ($tempUser && $tempUser->hasRole($role)) {
                        $user = $tempUser;
                        $userType = $modelInfo['type'];
                        $passwordField = $modelInfo['password_field'];
                        break;
                    }
                }
        }

        if ($user) {
            return [
                'user' => $user,
                'user_type' => $userType,
                'password_field' => $passwordField,
                'email' => $email,
                'role' => $role
            ];
        }

        return null;
    }

    protected function sendResetEmail($userInfo, $token)
    {
        $user = $userInfo['user'];
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $userInfo['email'],
            'role' => $userInfo['role']
        ]));

        // Get user's name based on user type
        $userName = $this->getUserName($user, $userInfo['user_type']);

        // Send email (you can customize this based on your mail setup)
        Mail::send('auth.emails.password-reset', [
            'user' => $user,
            'userName' => $userName,
            'resetUrl' => $resetUrl,
            'role' => $userInfo['role']
        ], function ($message) use ($userInfo, $userName) {
            $message->to($userInfo['email'], $userName);
            $message->subject('Reset Password Request');
        });
    }

    protected function getUserName($user, $userType)
    {
        switch ($userType) {
            case 'student':
                return $user->first_name . ' ' . $user->last_name;
            case 'instructor':
                return $user->instructor_name ;
            case 'parent':
                return $user->name;
            case 'user':
                return $user->name;
            default:
                return $user->name ?? $user->first_name . ' ' . $user->last_name ?? 'User';
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email,
            'role' => $request->role
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'role' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        // Find the password reset record
        $passwordReset = PasswordReset::where('email', $request->email)
                                    ->where('role', $request->role)
                                    ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'This password reset token is invalid.']);
        }

        // Check if token is expired (24 hours)
        if (Carbon::parse($passwordReset->created_at)->addHours(24)->isPast()) {
            return back()->withErrors(['email' => 'This password reset token has expired.']);
        }

        // Find user and update password
        $userInfo = $this->findUserByEmailAndRole($request->email, $request->role);

        if (!$userInfo) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Update password based on user type
        $user = $userInfo['user'];
        $passwordField = $userInfo['password_field'];

        $user->{$passwordField} = Hash::make($request->password);
        $user->save();

        // Delete the password reset record
        $passwordReset->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }

    public function validateForgotPassword(Request $request)
    {
        $email = $request->input('email');
        $role = $request->input('role');

        try {
            // Validate role
            $existingRoles = Role::pluck('name')->toArray();
            $validRoles = array_merge(['student', 'instructor', 'admin', 'manager', 'parent'], $existingRoles);

            if (!in_array($role, $validRoles)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Invalid role selected',
                ], 400);
            }

            // Check if user exists with this email and role
            $userInfo = $this->findUserByEmailAndRole($email, $role);

            if ($userInfo) {
                return response()->json([
                    'valid' => true,
                    'message' => 'User found',
                ]);
            } else {
                return response()->json([
                    'valid' => false,
                    'message' => 'No user found with this email and role combination',
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'An error occurred during validation',
            ], 500);
        }
    }
}
