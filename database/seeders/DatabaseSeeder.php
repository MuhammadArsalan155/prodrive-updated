<?php

namespace Database\Seeders;

use App\Models\CourseInstallmentPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminUserSeeder::class,
            DynamicPermissionSeeder::class,
           // CourseInstallmentPlan::class
        
        ]);
        // \App\Models\User::factory(10)->create();
        // User::create([
        //     'name' => "Admin",
        //     'email' => "Admin@gmail.com",
        //     'email_verified_at' => now(),
        //     'password' => Hash::make(123456), // password
        //     'remember_token' => Str::random(10),
        // ]);
    }
}
