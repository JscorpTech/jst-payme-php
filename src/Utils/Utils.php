<?php

namespace JscorpTech\Payme\Utils;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Utils
{
    public static function callback($callback)
    {
        try {
            App::make($callback[0])->$callback[1]();
        } catch (\Exception $e) {
            Log::error("Payme Success Handler error");
        }
    }
}
