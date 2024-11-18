<?php

namespace JscorpTech\Payme\Utils;

use Illuminate\Support\Facades\Response as FacadesResponse;

trait Response
{
    public function success(array $result)
    {
        return FacadesResponse::json([
            "result" => $result
        ]);
    }
    public function error($e)
    {
        return FacadesResponse::json([
            "id" => $e->request_id,
            "result" => null,
            "error" => $e->error
        ]);
    }
}
