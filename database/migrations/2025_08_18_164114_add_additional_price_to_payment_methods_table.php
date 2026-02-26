<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalPriceToPaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->decimal('additional_price', 10, 2)->default(0)->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('additional_price');
        });
    }
}
