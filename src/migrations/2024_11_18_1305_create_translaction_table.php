<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymeTransaction extends Migration
{
    public function up()
    {
        Schema::create("payme_transactions", function (Blueprint $table) {
            $table->bigInteger("amount");
            $table->bigInteger("transaction_id");
            $table->bigInteger("time");
            $table->bigInteger("create_time");
            $table->bigInteger("perform_time");
            $table->bigInteger("cancel_time");
            $table->bigInteger("state");
            $table->bigInteger("reason");
            $table->bigInteger("order_id");
        });
    }

    public function down()
    {
        Schema::dropIfExists("payme_transactions");
    }
}
