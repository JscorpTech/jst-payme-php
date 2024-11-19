<?php


namespace JscorpTech\Payme\Enums;


class ErrorEnum
{
    const TIMEOUT = 43200000;

    const INTERNAL_SYSTEM              = -32400;
    const INSUFFICIENT_PRIVILEGE       = -32504;
    const INVALID_JSON_RPC_OBJECT      = -32600;
    const METHOD_NOT_FOUND             = -32601;
    const INVALID_AMOUNT               = -31001;
    const TRANSACTION_NOT_FOUND        = -31003;
    const INVALID_ACCOUNT              = -31050;
    const COULD_NOT_CANCEL             = -31007;
    const COULD_NOT_PERFORM            = -31008;
}
