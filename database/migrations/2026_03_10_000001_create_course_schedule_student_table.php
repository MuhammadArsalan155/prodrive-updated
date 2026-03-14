<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseScheduleStudentTable extends Migration
{
    public function up()
    {
        Schema::create('course_schedule_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_schedule_id')->constrained('course_schedules')->onDelete('cascade');
            $table->unsignedBigInteger('assigned_by')->nullable(); // instructor_id who assigned it
            $table->timestamps();
            $table->unique(['student_id', 'course_schedule_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_schedule_student');
    }
}
