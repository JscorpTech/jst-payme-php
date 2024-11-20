<?php

namespace JscorpTech\Payme\Utils;

use Illuminate\Support\Facades\Log;

class Handler
{

    /**
     * To'lov muvofaqiyatli yakunlanganda
     */
    public static function success($data = null)
    {
        Log::info("To'lov muvofaqiyatli yakunlandi: " . $data->order_id);
    }

    /**
     * To'lov bekor qilinganda
     */
    public static function cancel($data = null)
    {
        Log::info("To'lov bekor qilindi: " . $data->order_id);
    }
}
