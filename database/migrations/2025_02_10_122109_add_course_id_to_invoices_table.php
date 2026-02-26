<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseIdToInvoicesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('invoices', 'course_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->unsignedBigInteger('course_id')->nullable(); // Adjust to match your original intent
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('invoices', 'course_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('course_id');
            });
        }
    }
}
