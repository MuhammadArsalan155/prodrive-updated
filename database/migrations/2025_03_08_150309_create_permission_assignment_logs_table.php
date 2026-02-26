<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionAssignmentLogsTable extends Migration
{
    public function up()
    {
        Schema::create('permission_assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('permission_id')->nullable();
            $table->enum('action', ['assign', 'revoke']);
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->foreign('admin_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permission_assignment_logs');
    }
}