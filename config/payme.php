<?php

use Illuminate\Support\Env;

return [
    "order_table" => Env::get("PAYME_ORDER_TABLE", "payme_orders"),
    "login" => Env::get("PAYME_LOGIN", "Paycom"),
    "key" => Env::get("PAYME_KEY", "1234567890"),
    "merchant_id" => Env::get("PAYME_MERCHANT_ID", "1234567890"),
    "success_callback" => null,
    "cancel_callback" => null,
];
