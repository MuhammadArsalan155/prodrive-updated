<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstallmentPlanToCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('has_installment_plan')->default(false);
            $table->foreignId('course_installment_plan_id')
              ->nullable()
              ->constrained('course_installment_plans')
              ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeignKey(['course_installment_plan_id']);
            $table->dropColumn(['has_installment_plan', 'course_installment_plan_id']);
        });
    }
}