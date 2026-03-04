<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practical_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedTinyInteger('session_number'); // 1, 2, 3, ...
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();   // auto: start_time + 2 hours
            $table->decimal('duration_hours', 4, 1)->default(2.0);
            $table->enum('status', ['scheduled', 'completed', 'failed', 'not_appeared', 'cancelled'])
                  ->default('scheduled');
            $table->text('instructor_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practical_sessions');
    }
};
