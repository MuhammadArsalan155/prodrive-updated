<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a list of all roles with their user counts
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Eager load roles with their permissions
        $roles = Role::with('permissions')->get();

        // Prepare role counts more efficiently
        $roleCounts = $this->calculateRoleCounts($roles);

        return view('admin.role-permission.index', compact('roles', 'roleCounts'));
    }

    /**
     * Calculate role counts for different model types
     *
     * @param \Illuminate\Support\Collection $roles
     * @return array
     */
    protected function calculateRoleCounts($roles)
    {
        $modelTypes = [
            'student_count' => Student::class,
            'user_count' => User::class,
            'instructor_count' => Instructor::class,
        ];

        $roleCounts = [];

        foreach ($roles as $role) {
            $counts = [];
            foreach ($modelTypes as $key => $modelClass) {
                $counts[$key] = DB::table('model_has_roles')->where('role_id', $role->id)->where('model_type', $modelClass)->count();
            }
            $roleCounts[$role->id] = $counts;
        }

        return $roleCounts;
    }

    /**
     * Show the form to create a new role
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.role-permission.create');
    }

    /**
     * Store a new role
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'display_name' => 'required|max:255',
            'description' => 'nullable|max:500',
            'is_system_role' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $adminId = Auth::id();

            // Create the role
            $role = Role::create($validatedData);

            // Log role creation
            DB::table('permission_assignment_logs')->insert([
                'admin_id' => $adminId,
                'role_id' => $role->id,
                'action' => 'assign',
                'reason' => 'Role creation',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', "Role {$role->display_name} created successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Creation Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to create role. Please try again.');
        }
    }

    /**
     * Show the form to edit a role
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        // Calculate role counts
        //$roleCounts = $this->calculateRoleCounts(collect([$role]));

        return view('admin.role-permission.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . '|max:255',
            'display_name' => 'required|max:255',
            'description' => 'nullable|max:500',
            'is_system_role' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $adminId = Auth::id();

            if (!$role->is_system_role) {
                if ($request->has('is_system_role')) {
                    $validatedData['is_system_role'] = $request->input('is_system_role') ? 1 : 0;
                } else {
                    $validatedData['is_system_role'] = $role->is_system_role;
                }
            }
            $role->update($validatedData);
            
            
            DB::table('permission_assignment_logs')->insert([
                'admin_id' => $adminId,
                'role_id' => $role->id,
                'action' => 'assign',
                'reason' => 'Role updated',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', "Role {$role->display_name} updated successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Update Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to update role. Please try again.')->withInput();
        }
    }
    /**
     * Show the form to edit role permissions
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editPermissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all()->groupBy('group');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.role-permission.edit-permission', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Update permissions for a specific role
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissionIds = $request->input('permissions', []);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get current admin
            $adminId = Auth::id();

            // Get current permissions
            $currentPermissionIds = $role->permissions->pluck('id')->toArray();

            // Determine added and removed permissions
            $addedPermissions = array_diff($permissionIds, $currentPermissionIds);
            $removedPermissions = array_diff($currentPermissionIds, $permissionIds);

            // Sync permissions
            $role->permissions()->sync($permissionIds);

            // Prepare log entries
            $logEntries = [];

            // Log added permissions
            foreach ($addedPermissions as $permissionId) {
                $logEntries[] = [
                    'admin_id' => $adminId,
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                    'action' => 'assign',
                    'reason' => 'Role permission update',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Log removed permissions
            foreach ($removedPermissions as $permissionId) {
                $logEntries[] = [
                    'admin_id' => $adminId,
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                    'action' => 'revoke',
                    'reason' => 'Role permission update',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert log entries if any
            if (!empty($logEntries)) {
                DB::table('permission_assignment_logs')->insert($logEntries);
            }

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', "Permissions for role {$role->display_name} updated successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Permission Update Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to update role permissions. Please try again.');
        }
    }

    /**
     * Delete a role
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deletion of system roles
        if ($role->is_system_role) {
            return redirect()->back()->with('error', 'System roles cannot be deleted.');
        }

        DB::beginTransaction();

        try {
            $adminId = Auth::id();

            // Log role deletion
            DB::table('permission_assignment_logs')->insert([
                'admin_id' => $adminId,
                'role_id' => $role->id,
                'action' => 'revoke',
                'reason' => 'Role deletion',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Remove role assignments
            DB::table('model_has_roles')->where('role_id', $role->id)->delete();

            // Remove role permissions
            $role->permissions()->detach();

            // Delete the role
            $role->delete();

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', "Role {$role->display_name} deleted successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Deletion Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to delete role. Please try again.');
        }
    }

    /**
     * Assign roles to models
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignRoles(Request $request)
    {
        $validatedData = $request->validate([
            'model_type' => 'required|in:User,Student,Instructor',
            'model_id' => 'required|integer',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $adminId = Auth::id();
            $modelType = 'App\\Models\\' . $validatedData['model_type'];
            $modelId = $validatedData['model_id'];
            $roleIds = $validatedData['role_ids'];
            $reason = $validatedData['reason'] ?? 'Role assignment';

            // Remove existing roles
            DB::table('model_has_roles')->where('model_type', $modelType)->where('model_id', $modelId)->delete();

            // Prepare role assignment logs
            $logEntries = [];
            $roleAssignments = [];

            foreach ($roleIds as $roleId) {
                // Prepare role assignment
                $roleAssignments[] = [
                    'role_id' => $roleId,
                    'model_id' => $modelId,
                    'model_type' => $modelType,
                ];

                // Prepare log entry
                $logEntries[] = [
                    'admin_id' => $adminId,
                    'role_id' => $roleId,
                    'model_id' => $modelId,
                    'model_type' => $modelType,
                    'action' => 'assign',
                    'reason' => $reason,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert role assignments
            if (!empty($roleAssignments)) {
                DB::table('model_has_roles')->insert($roleAssignments);
            }

            // Bulk insert log entries
            if (!empty($logEntries)) {
                DB::table('permission_assignment_logs')->insert($logEntries);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Roles assigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Assignment Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to assign roles. Please try again.');
        }
    }

    /**
     * Show role assignment page
     *
     * @return \Illuminate\View\View
     */
    public function showAssignRoles()
    {
        $roles = Role::all();
        $modelAssignments = $this->getModelAssignments();

        return view('admin.role-permission.assign-roles', compact('roles', 'modelAssignments'));
    }

    /**
     * Retrieve existing model assignments
     *
     * @return array
     */
    protected function getModelAssignments()
    {
        $modelTypes = [
            'User' => User::class,
            'Student' => Student::class,
            'Instructor' => Instructor::class,
        ];

        $assignments = [];

        foreach ($modelTypes as $key => $modelClass) {
            $assignments[$key] = $modelClass::with('roles')->get();
        }

        return $assignments;
    }

    /**
     * Get models based on model type
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModels(Request $request)
    {
        $modelType = $request->input('model_type');

        $modelMap = [
            'User' => User::class,
            'Student' => Student::class,
            'Instructor' => Instructor::class,
        ];

        if (!isset($modelMap[$modelType])) {
            return response()->json([]);
        }

        $model = $modelMap[$modelType];

        // Fetch models with their current roles
        $models = $model::with('roles')->get();

        return response()->json($models);
    }

    /**
     * Get roles for a specific model
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModelRoles(Request $request)
    {
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');

        $modelMap = [
            'User' => User::class,
            'Student' => Student::class,
            'Instructor' => Instructor::class,
        ];

        if (!isset($modelMap[$modelType])) {
            return response()->json(['error' => 'Invalid model type'], 400);
        }

        $model = $modelMap[$modelType]::findOrFail($modelId);

        return response()->json([
            'role_ids' => $model->roles->pluck('id')->toArray(),
        ]);
    }
}
