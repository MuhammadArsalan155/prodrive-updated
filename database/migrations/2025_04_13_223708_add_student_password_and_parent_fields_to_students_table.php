<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentPasswordAndParentFieldsToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_password')->nullable()->after('email');
            $table->string('parent_name')->nullable()->after('completion_date');
            $table->string('parent_email')->nullable()->after('parent_name');
            $table->string('parent_password')->nullable()->after('parent_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('student_password');
            $table->dropColumn('parent_name');
            $table->dropColumn('parent_email');
            $table->dropColumn('parent_password');
        });
    }
};