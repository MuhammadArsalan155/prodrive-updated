<?php

namespace Database\Seeders;

use App\Models\CourseInstallmentPlan;
use Illuminate\Database\Seeder;

class CourseInstallmentPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CourseInstallmentPlan::createDefaultPlans();
    }
}
