<?php



namespace jscorptech\Payme\Utils;

use Illuminate\Support\Carbon;

trait Time
{

    static public function get_time()
    {
        return Carbon::now()->timestamp * 1000;
    }
}
