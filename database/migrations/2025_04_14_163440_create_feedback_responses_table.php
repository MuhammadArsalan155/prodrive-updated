<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('feedback_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedback_question_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('user_id'); // Student or instructor ID
            $table->string('user_type'); // 'student' or 'instructor'
            $table->boolean('response'); // yes (true) or no (false)
            $table->text('comments')->nullable();
            $table->integer('class_order'); 
            $table->timestamps();
            
            $table->foreign('feedback_question_id')
                  ->references('id')
                  ->on('feedback_questions')
                  ->onDelete('cascade');
                  
            $table->foreign('course_id')
                  ->references('id')
                  ->on('courses')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_responses');
    }
}
