<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortTable extends Migration
{
    public function up()
    {
        Schema::create("payme_orders", function (Blueprint $table) {
            $table->bigInteger("amount");
            $table->boolean("state");
        });
    }

    public function down()
    {
        Schema::dropIfExists("payme_orders");
    }
}
