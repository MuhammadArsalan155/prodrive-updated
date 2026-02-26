<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('total_theory_classes')->nullable()->after('theory_hours');
            $table->integer('total_practical_classes')->nullable()->after('practical_hours');
        });
    }

    
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('total_theory_classes');
            $table->dropColumn('total_practical_classes');
        });
    }
};