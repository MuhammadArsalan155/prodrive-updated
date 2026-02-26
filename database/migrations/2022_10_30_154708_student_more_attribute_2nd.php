<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentMoreAttribute2nd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('address');
            $table->string('joining_date')->default(DB::raw('CURRENT_DATE'));
            $table->string('completion_date')->nullable();
            $table->string('hours_theory')->nullable();
            $table->string('hours_practical')->nullable();
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
            //
        });
    }
}

//ALTER TABLE students 
//+MODIFY COLUMN joining_date DATE DEFAULT (CURRENT_DATE);