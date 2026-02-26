<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentsTable extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('instructor_id')->constrained('instructors');
            $table->timestamp('enrollment_date');
            $table->string('status');
            $table->integer('total_theory_hours_completed')->default(0);
            $table->integer('total_practical_hours_completed')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
}
