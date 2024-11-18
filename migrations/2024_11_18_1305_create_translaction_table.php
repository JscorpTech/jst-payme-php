<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create("payme_transactions", function (Blueprint $table) {
            $table->id();
            $table->text("transaction_id");
            $table->bigInteger("time");
            $table->bigInteger("create_time");
            $table->bigInteger("perform_time")->nullable();
            $table->bigInteger("cancel_time")->nullable();
            $table->integer("state")->default(1);
            $table->integer("reason")->nullable();
            $table->foreignId("order_id")->constrained(config("payme.order_table"), "id");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("payme_transactions");
    }
};
