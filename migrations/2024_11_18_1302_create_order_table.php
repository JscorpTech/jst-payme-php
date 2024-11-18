<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::create("payme_orders", function (Blueprint $table) {
            $table->id();
            $table->bigInteger("amount");
            $table->boolean("state")->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists("payme_orders");
    }
};
