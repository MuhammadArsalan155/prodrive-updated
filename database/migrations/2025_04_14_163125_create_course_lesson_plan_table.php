<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLessonPlanTable extends Migration
{
    public function up(): void
    {
        Schema::create('course_lesson_plan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('lesson_plan_id');
            $table->string('class_type'); // 'theory' or 'practical'
            $table->integer('class_order'); // Which class number (1st, 2nd, etc.)
            $table->timestamps();
            
            $table->foreign('course_id')
                  ->references('id')
                  ->on('courses')
                  ->onDelete('cascade');
                  
            $table->foreign('lesson_plan_id')
                  ->references('id')
                  ->on('lesson_plans')
                  ->onDelete('cascade');
                  
            $table->unique(['course_id', 'class_type', 'class_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lesson_plan');
    }
}
