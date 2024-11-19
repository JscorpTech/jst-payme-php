<?php


namespace JscorpTech\Payme\Enums;


enum ErrorEnum: int
{
    case TIMEOUT = 43200000;

    case INTERNAL_SYSTEM              = -32400;
    case INSUFFICIENT_PRIVILEGE       = -32504;
    case INVALID_JSON_RPC_OBJECT      = -32600;
    case METHOD_NOT_FOUND             = -32601;
    case INVALID_AMOUNT               = -31001;
    case TRANSACTION_NOT_FOUND        = -31003;
    case INVALID_ACCOUNT              = -31050;
    case COULD_NOT_CANCEL             = -31007;
    case COULD_NOT_PERFORM            = -31008;
}
