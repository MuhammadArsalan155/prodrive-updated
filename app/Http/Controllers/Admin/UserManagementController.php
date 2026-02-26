<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    /**
     * Display a consolidated listing of all users across different models.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch and transform standard users
        $systemUsers = User::with('roles')
            ->get()
            ->map(function ($user) {
                $user->user_type = 'System User';
                return $user;
            });

        // Get all student records
        $students = Student::with('roles')->get()
            ->map(function ($student) {
                $student->name = $student->first_name . ' ' . $student->last_name;
                $student->user_type = 'Students';
                return $student;
            });

        // Get all parent records from StudentParent model
        $parents = StudentParent::with('roles')->get()
            ->map(function ($parent) {
                $parent->user_type = 'Parents';
                return $parent;
            });

        // Fetch and transform instructors
        $instructors = Instructor::with('roles')
            ->get()
            ->map(function ($instructor) {
                $instructor->name = $instructor->instructor_name;
                $instructor->user_type = 'Instructors';
                return $instructor;
            });

        $users = collect()->concat($systemUsers)->concat($students)->concat($parents)->concat($instructors);

        // Get all available roles
        $roles = Role::all();

        return view('admin.user-management.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
    /**
     * Show user details
     *
     * @param string $type
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($type, $id)
    {
        switch ($type) {
            case 'students':
                $user = Student::findOrFail($id);
                $userName = $user->first_name . ' ' . $user->last_name;
                break;
            case 'parents':
                $user = StudentParent::findOrFail($id);
                $userName = $user->name;
                break;
            case 'instructors':
                $user = Instructor::findOrFail($id);
                $userName = $user->instructor_name;
                break;
            default:
                $user = User::findOrFail($id);
                $userName = $user->name;
                $type = 'users';
        }

        // Get user's permissions
        $userPermissions = $user->getAllPermissions();

        // If it's an AJAX request, return a partial view
        if (request()->ajax()) {
            return view('admin.user-management.user-details', [
                'user' => $user,
                'userName' => $userName,
                'userPermissions' => $userPermissions,
                'type' => $type,
            ]);
        }

        // Otherwise, return full view
        return view('admin.user-role-permission.show', [
            'user' => $user,
            'userName' => $userName,
            'userPermissions' => $userPermissions,
            'type' => $type,
        ]);
    }

    /**
     * Edit roles for a specific user across different models.
     *
     * @param  string  $type
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($type, $id)
    {
        switch ($type) {
            case 'students':
                $user = Student::findOrFail($id);
                $userName = $user->first_name . ' ' . $user->last_name;
                break;
            case 'parents':
                $user = StudentParent::findOrFail($id);
                $userName = $user->name;
                break;
            case 'instructors':
                $user = Instructor::findOrFail($id);
                $userName = $user->instructor_name;
                break;
            default:
                $user = User::findOrFail($id);
                $userName = $user->name;
                $type = 'users';
        }
    
        // Get all roles and permissions
        $roles = Role::all();
        $permissions = Permission::all();
    
        // Get current role IDs for pre-selecting in the form
        $userRoleIds = $user->roles->pluck('id')->toArray();
    
        // Get all permissions the user has through their roles
        $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();
    
        return view('admin.user-management.edit', [
            'user' => $user,
            'userName' => $userName,
            'roles' => $roles,
            'permissions' => $permissions,
            'userRoleIds' => $userRoleIds,
            'userPermissions' => $userPermissions,
            'type' => $type,
        ]);
    }
    
    /**
     * Update roles for a specific user across different models.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $type
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $type, $id)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'user_type' => 'required|in:users,students,parents,instructors',
        ]);
    
        try {
            DB::beginTransaction();
    
            switch ($type) {
                case 'students':
                    $user = Student::findOrFail($id);
                    break;
                case 'parents':
                    $user = StudentParent::findOrFail($id);
                    break;
                case 'instructors':
                    $user = Instructor::findOrFail($id);
                    break;
                default:
                    $user = User::findOrFail($id);
                    $type = 'users';
            }
    
            // Sync roles with logging
            $roleIds = $request->input('roles', []) ?? [];
            
            // Special handling for parent type
            if ($type === 'parents') {
                $parentRole = Role::where('name', 'parent')->first();
                if ($parentRole && !in_array($parentRole->id, $roleIds)) {
                    $roleIds[] = $parentRole->id;
                }
            }
            
            // If it's a student type, ensure they don't lose the student role
            if ($type === 'students') {
                $studentRole = Role::where('name', 'student')->first();
                if ($studentRole && !in_array($studentRole->id, $roleIds)) {
                    $roleIds[] = $studentRole->id;
                }
            }
            
            $result = $user->syncRoles($roleIds, 'Manual role update via admin panel');
    
            if (!$result) {
                throw new \Exception('Failed to sync roles');
            }
    
            // Handle any additional fields to update
            if ($type === 'parents' && $request->filled('name')) {
                $user->update([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                ]);
            }
    
            DB::commit();
    
            // Return JSON response for AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Roles for {$type} user updated successfully",
                    'redirectUrl' => route('admin.users.index'),
                ]);
            }
    
            // Fallback for non-AJAX requests
            return redirect()
                ->route('admin.users.index')
                ->with('success', "Roles for {$type} user updated successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user roles: ' . $e->getMessage());
    
            // Return JSON response for AJAX
            if ($request->ajax()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Error updating user roles. ' . $e->getMessage(),
                    ],
                    500,
                );
            }
    
            // Fallback for non-AJAX requests
            return redirect()->back()->with('error', 'Error updating user roles. Please try again.')->withInput();
        }
    }

    /**
     * Get permissions for selected roles
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRolePermissions(Request $request)
    {
        $roleIds = $request->input('role_ids', []);

        $permissions = Permission::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('roles.id', $roleIds);
        })->get();

        return response()->json([
            'permissions' => $permissions,
        ]);
    }

   
    public function logs()
    {
        $logs = DB::table('permission_assignment_logs')->join('users as admin', 'admin.id', '=', 'permission_assignment_logs.admin_id')->join('users', 'users.id', '=', 'permission_assignment_logs.model_id')->leftJoin('roles', 'roles.id', '=', 'permission_assignment_logs.role_id')->leftJoin('permissions', 'permissions.id', '=', 'permission_assignment_logs.permission_id')->select('permission_assignment_logs.id', 'admin.name as admin_name', 'users.name as user_name', 'roles.name as role_name', 'permissions.name as permission_name', 'permission_assignment_logs.action', 'permission_assignment_logs.created_at')->orderBy('permission_assignment_logs.created_at', 'desc')->paginate(15);

        return view('admin.user-management.logs', compact('logs'));
    }
}