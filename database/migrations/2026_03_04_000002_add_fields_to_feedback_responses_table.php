<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_responses', function (Blueprint $table) {
            // 'theory' or 'practical'
            $table->string('class_type', 20)->nullable()->after('class_order');
            // FK to course_lesson_plan pivot row (no Eloquent model needed)
            $table->unsignedBigInteger('course_lesson_plan_id')->nullable()->after('class_type');
        });

        // Backfill class_type and course_lesson_plan_id for any existing rows.
        // Match via: feedback_response.feedback_question_id → feedback_questions.lesson_plan_id
        //            feedback_response.course_id + class_order + lesson_plan_id → course_lesson_plan
        DB::statement("
            UPDATE feedback_responses fr
            JOIN feedback_questions fq ON fq.id = fr.feedback_question_id
            JOIN course_lesson_plan clp
                ON clp.course_id = fr.course_id
               AND clp.class_order = fr.class_order
               AND clp.lesson_plan_id = fq.lesson_plan_id
            SET fr.class_type = clp.class_type,
                fr.course_lesson_plan_id = clp.id
            WHERE fr.class_type IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('feedback_responses', function (Blueprint $table) {
            $table->dropColumn(['class_type', 'course_lesson_plan_id']);
        });
    }
};
