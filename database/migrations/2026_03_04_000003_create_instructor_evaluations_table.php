<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('instructors')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();

            // Ratings 1-5
            $table->unsignedTinyInteger('performance_rating')->default(3);
            $table->unsignedTinyInteger('behavior_rating')->default(3);
            $table->unsignedTinyInteger('attendance_rating')->default(3);
            $table->unsignedTinyInteger('overall_rating')->default(3);

            // Narrative fields
            $table->text('performance_notes')->nullable();
            $table->text('behavior_notes')->nullable();
            $table->text('recommendations')->nullable();

            $table->boolean('is_recommended_for_certificate')->default(true);

            $table->timestamps();

            // One evaluation per student per course
            $table->unique(['student_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_evaluations');
    }
};
