<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToEnrollmentsTable extends Migration
{
    public function up()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['student_id', 'course_id', 'status']);
        });
    }

    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'course_id', 'status']);
        });
    }
}