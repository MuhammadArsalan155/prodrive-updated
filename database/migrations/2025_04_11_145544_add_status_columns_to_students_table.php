<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnsToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Add theory status columns
            $table->enum('theory_status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('theory_completion_date')->nullable();
            
            // Add practical status columns
            $table->enum('practical_status', ['pending', 'assigned', 'completed', 'failed', 'not_appeared'])->default('pending');
            $table->timestamp('practical_completion_date')->nullable();
            $table->unsignedBigInteger('practical_schedule_id')->nullable();
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
            $table->dropColumn([
                'theory_status', 
                'theory_completion_date', 
                'practical_status', 
                'practical_completion_date',
                'practical_schedule_id'
            ]);
        });
    }
}