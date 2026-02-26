<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToCourseSchedulesTable extends Migration
{
    public function up()
    {
        Schema::table('course_schedules', function (Blueprint $table) {
            $table->index(['date', 'start_time', 'instructor_id']);
            $table->index(['session_type', 'is_active']);
        });
    }

    public function down()
    {
        Schema::table('course_schedules', function (Blueprint $table) {
            $table->dropIndex(['date', 'start_time', 'instructor_id']);
            $table->dropIndex(['session_type', 'is_active']);
        });
    }
}
