<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCoursesTableAddCourseDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('course_type');
            $table->text('description')->nullable();
            $table->integer('theory_hours')->default(0);
            $table->integer('practical_hours')->default(0);
            $table->boolean('is_active')->default(true);
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
            $table->dropColumn(['course_type', 'description', 'theory_hours', 'practical_hours', 'is_active']);
        });
    }
}
