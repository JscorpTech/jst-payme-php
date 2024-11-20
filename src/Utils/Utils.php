<?php

namespace JscorpTech\Payme\Utils;

use Illuminate\Support\Facades\Log;

class Utils
{
    public static function callback($callback, $data = null)
    {
        try {
            call_user_func($callback, $data);
        } catch (\Exception $e) {
            Log::error("Payme Handler error: ".$e->getMessage(). ' : '.$e->getLine(). " : ".$e->getFile());
        }
    }
}
