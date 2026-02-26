<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackQuestionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_plan_id');
            $table->string('question_text');
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->foreign('lesson_plan_id')
                  ->references('id')
                  ->on('lesson_plans')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_questions');
    }
}
