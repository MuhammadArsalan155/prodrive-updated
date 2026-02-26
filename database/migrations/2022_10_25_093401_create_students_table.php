<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string("first_name");
            $table->string("last_name");
            $table->string("email");
            $table->string("student_contact");
            $table->string("student_dob");
            $table->string("profile_photo")->nullable();
            $table->integer("instructor_id");
            $table->integer("course_id");
            $table->integer('course_status')->default(0); //0 In-progress 1 completed
            $table->integer('payment_status')->default(0); //0 not paid 1 Paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
