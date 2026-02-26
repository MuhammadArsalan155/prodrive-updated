<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['transaction_id', 'status']);
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['transaction_id', 'status']);
        });
    }
}