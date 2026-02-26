<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         // Truncate the permissions table (deletes all records and resets IDs)
         DB::table('permissions')->truncate();
        // Comprehensive list of permissions with groups
        $permissionGroups = [
            'User Management' => [
                ['name' => 'manage-users', 'display_name' => 'Manage Users', 'description' => 'Create, update and delete users', 'is_system_permission' => true],
                ['name' => 'view-users', 'display_name' => 'View Users', 'description' => 'View user details', 'is_system_permission' => true],
                ['name' => 'edit-user-profile', 'display_name' => 'Edit User Profile', 'description' => 'Edit user profile information', 'is_system_permission' => false],
            ],
            'Role Management' => [
                ['name' => 'manage-roles', 'display_name' => 'Manage Roles', 'description' => 'Create, update and delete roles', 'is_system_permission' => true],
                ['name' => 'view-roles', 'display_name' => 'View Roles', 'description' => 'View role details', 'is_system_permission' => true],
                ['name' => 'assign-roles', 'display_name' => 'Assign Roles', 'description' => 'Assign roles to users', 'is_system_permission' => true],
            ],
            'Permission Management' => [
                ['name' => 'manage-permissions', 'display_name' => 'Manage Permissions', 'description' => 'Assign and revoke permissions', 'is_system_permission' => true],
                ['name' => 'view-permissions', 'display_name' => 'View Permissions', 'description' => 'View permission details', 'is_system_permission' => true],
            ],
            'Course Management' => [
                ['name' => 'manage-courses', 'display_name' => 'Manage Courses', 'description' => 'Create, update and delete courses', 'is_system_permission' => true],
                ['name' => 'view-courses', 'display_name' => 'View Courses', 'description' => 'View course details', 'is_system_permission' => true],
                ['name' => 'enroll-students', 'display_name' => 'Enroll Students', 'description' => 'Enroll students in courses', 'is_system_permission' => true],
                ['name' => 'edit-course-content', 'display_name' => 'Edit Course Content', 'description' => 'Modify course materials and curriculum', 'is_system_permission' => false],
            ],
            'Grade Management' => [
                ['name' => 'manage-grades', 'display_name' => 'Manage Grades', 'description' => 'Enter and modify grades', 'is_system_permission' => true],
                ['name' => 'view-grades', 'display_name' => 'View Grades', 'description' => 'View grade details', 'is_system_permission' => true],
                ['name' => 'submit-grades', 'display_name' => 'Submit Grades', 'description' => 'Submit final grades for a course', 'is_system_permission' => false],
            ],
            'Reporting' => [
                ['name' => 'generate-reports', 'display_name' => 'Generate Reports', 'description' => 'Create and export system reports', 'is_system_permission' => true],
                ['name' => 'view-reports', 'display_name' => 'View Reports', 'description' => 'View system-generated reports', 'is_system_permission' => true],
            ]
        ];

        // Collect processed permission names
        $processedPermissions = [];

        // Process permissions
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permissionData) {
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
                    
                    if ($existingPermission->is_system_permission !== $permissionData['is_system_permission']) {
                        $existingPermission->is_system_permission = $permissionData['is_system_permission'];
                        $hasChanges = true;
                    }

                    // Save changes if any
                    if ($hasChanges) {
                        $existingPermission->save();
                        $this->command->info("Permission {$permissionData['name']} updated.");
                    } else {
                        $this->command->info("Permission {$permissionData['name']} already exists, no changes needed.");
                    }
                } else {
                    // Create new permission
                    $newPermission = Permission::create([
                        'name' => $permissionData['name'],
                        'display_name' => $permissionData['display_name'],
                        'description' => $permissionData['description'],
                        'is_system_permission' => $permissionData['is_system_permission'],
                    ]);
                    $this->command->info("Permission {$permissionData['name']} created.");
                }

                $processedPermissions[] = $permissionData['name'];
            }
        }

        // Remove permissions no longer in the predefined list
        Permission::whereNotIn('name', $processedPermissions)->delete();

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Permission seeding completed.');
    }

    /**
     * Assign permissions to roles with more granular control
     */
    protected function assignPermissionsToRoles()
    {
        // Retrieve roles
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $instructorRole = Role::where('name', 'instructor')->first();
        $studentRole = Role::where('name', 'student')->first();
        $parentRole = Role::where('name', 'parent')->first();

        // Role-specific permission mappings
        $rolePermissionMap = [
            'admin' => Permission::all(), // All permissions
            'manager' => Permission::whereIn('name', [
                // User Management
                'view-users', 'manage-users', 'edit-user-profile',
                // Role Management
                'view-roles', 'assign-roles',
                // Permission Management
                'view-permissions',
                // Course Management
                'manage-courses', 'view-courses', 'enroll-students', 'edit-course-content',
                // Reporting
                'generate-reports', 'view-reports'
            ])->get(),
            'instructor' => Permission::whereIn('name', [
                // Course Management
                'view-courses', 'edit-course-content',
                // Grade Management
                'manage-grades', 'view-grades', 'submit-grades'
            ])->get(),
            'student' => Permission::whereIn('name', [
                // Course Management
                'view-courses',
                // Grade Management
                'view-grades'
            ])->get(),
            'parent' => Permission::whereIn('name', [
                // Grade Management
                'view-grades'
            ])->get()
        ];

        // Sync permissions for each role
        foreach ($rolePermissionMap as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            
            if ($role && $permissions->isNotEmpty()) {
                $role->permissions()->sync($permissions->pluck('id')->toArray());
                $this->command->info("Permissions assigned to {$roleName} role.");
            }
        }
    }
}