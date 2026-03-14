<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_attendance', function (Blueprint $table) {
            // Which sequential class this was for the student (1=first, 2=second, ...)
            $table->unsignedTinyInteger('class_order')->nullable()->after('notes');
            // 'theory' or 'practical'
            $table->string('class_type', 20)->nullable()->after('class_order');
            // When the instructor marked this session as complete
            $table->timestamp('completed_at')->nullable()->after('class_type');
        });
    }

    public function down(): void
    {
        Schema::table('session_attendance', function (Blueprint $table) {
            $table->dropColumn(['class_order', 'class_type', 'completed_at']);
        });
    }
};
