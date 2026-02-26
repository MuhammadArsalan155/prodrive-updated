<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseInstallmentPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_installment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('Name');
            $table->integer('number_of_installments');
            $table->decimal('first_installment_percentage', 5, 2);
            $table->decimal('subsequent_installment_percentage', 5, 2);
            $table->integer('days_between_installments')->nullable();
            $table->integer('course_duration_months')->nullable(); // New field
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_installment_plans');
    }
}
