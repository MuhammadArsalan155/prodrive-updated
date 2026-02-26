<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StudentParent extends Migration
{
    public function up()
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('contact')->nullable();
            $table->text('address')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('parents');
    }
}
