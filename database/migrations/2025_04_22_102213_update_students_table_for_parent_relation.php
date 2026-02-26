<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTableForParentRelation extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('instructor_id');
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('set null');
        });
        
        if (Schema::hasColumn('students', 'parent_name') && 
            Schema::hasColumn('students', 'parent_email') && 
            Schema::hasColumn('students', 'parent_password')) {
            
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn(['parent_name', 'parent_email', 'parent_password']);
            });
        }
    }

    
    public function down()
    {
        
        Schema::table('students', function (Blueprint $table) {
            $table->string('parent_name')->nullable();
            $table->string('parent_email')->nullable();
            $table->string('parent_password')->nullable();
        });
        
        // Drop the foreign key and column in a separate operation
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
}
