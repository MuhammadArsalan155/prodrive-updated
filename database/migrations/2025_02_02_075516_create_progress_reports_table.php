<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressReportsTable extends Migration
{
    public function up()
    {
        Schema::create('progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('instructor_id')->constrained('instructors');
            $table->foreignId('course_id')->constrained('courses');
            $table->text('performance_notes');
            $table->text('areas_of_improvement')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('progress_reports');
    }
}