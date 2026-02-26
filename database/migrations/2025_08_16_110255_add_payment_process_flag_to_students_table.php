<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPaymentProcessFlagToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('has_payment_process')->default(false)->after('joining_date');

            $table->index(['email', 'payment_status'], 'idx_students_email_payment_status');
            $table->index(['has_payment_process', 'created_at'], 'idx_students_payment_process_created');
            $table->index(['payment_status', 'created_at'], 'idx_students_payment_status_created');
        });


        $protectedCount = DB::table('students')
            ->where('created_at', '<', '2024-12-27') // ADJUST THIS DATE TO TODAY OR YOUR IMPLEMENTATION DATE
            ->update(['has_payment_process' => false]);


        $totalProtected = DB::table('students')
            ->where('has_payment_process', false)
            ->count();

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_students_email_payment_status');
            $table->dropIndex('idx_students_payment_process_created');
            $table->dropIndex('idx_students_payment_status_created');

            // Drop the column
            $table->dropColumn('has_payment_process');
        });


    }
}
