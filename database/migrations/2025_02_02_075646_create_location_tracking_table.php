<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationTrackingTable extends Migration
{
    public function up()
    {
        Schema::create('location_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('instructor_id')->constrained('instructors');
            $table->foreignId('course_schedule_id')->constrained('course_schedules');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamp('tracked_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('location_tracking');
    }
}