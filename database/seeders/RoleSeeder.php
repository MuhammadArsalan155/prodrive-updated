<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Predefined roles with comprehensive details
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'System administrator with full access to all features',
                'is_system_role' => true
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Manager with access to management features',
                'is_system_role' => true
            ],
            [
                'name' => 'instructor',
                'display_name' => 'Instructor',
                'description' => 'Instructor with access to teaching features',
                'is_system_role' => true
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Student with access to learning features',
                'is_system_role' => true
            ],
            [
                'name' => 'parent',
                'display_name' => 'Parent',
                'description' => 'Parent with access to monitor student progress',
                'is_system_role' => true
            ]
        ];

        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Collect created/updated role names
        $processedRoles = [];

        // Process each role
        foreach ($roles as $roleData) {
            // Try to find an existing role
            $existingRole = Role::where('name', $roleData['name'])->first();

            if ($existingRole) {
                // Update existing role if details have changed
                $hasChanges = false;
                
                if ($existingRole->display_name !== $roleData['display_name']) {
                    $existingRole->display_name = $roleData['display_name'];
                    $hasChanges = true;
                }
                
                if ($existingRole->description !== $roleData['description']) {
                    $existingRole->description = $roleData['description'];
                    $hasChanges = true;
                }
                
                if ($existingRole->is_system_role !== $roleData['is_system_role']) {
                    $existingRole->is_system_role = $roleData['is_system_role'];
                    $hasChanges = true;
                }

                // Save changes if any
                if ($hasChanges) {
                    $existingRole->save();
                    $this->command->info("Role {$roleData['name']} updated.");
                } else {
                    $this->command->info("Role {$roleData['name']} already exists, no changes needed.");
                }
            } else {
                // Create new role
                $newRole = Role::create($roleData);
                $this->command->info("Role {$roleData['name']} created.");
            }

            $processedRoles[] = $roleData['name'];
        }

        // Optional: Remove roles that are no longer in the predefined list
        Role::whereNotIn('name', $processedRoles)->delete();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Role seeding completed.');
    }
}