<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToLocationTrackingTable extends Migration
{
    public function up()
    {
        Schema::table('location_tracking', function (Blueprint $table) {
            $table->index(['student_id', 'tracked_at']);
            $table->index(['instructor_id', 'tracked_at']);
        });
    }

    public function down()
    {
        Schema::table('location_tracking', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'tracked_at']);
            $table->dropIndex(['instructor_id', 'tracked_at']);
        });
    }
}