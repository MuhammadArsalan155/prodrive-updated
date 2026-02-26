<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionAttendanceTable extends Migration
{
    public function up()
    {
        Schema::create('session_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('course_schedule_id')->constrained('course_schedules');
            $table->boolean('is_present')->default(false);
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('session_attendance');
    }
}
