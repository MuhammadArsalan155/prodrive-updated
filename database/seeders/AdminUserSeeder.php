<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'Admin@gmail.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('123456'), 
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
            $this->command->info('Admin role assigned to admin user.');
        }
    }
}