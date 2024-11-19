<?php


namespace JscorpTech\Payme\Enums;


class ErrorEnum
{
    public const TIMEOUT = 43200000;

    public const INTERNAL_SYSTEM              = -32400;
    public const INSUFFICIENT_PRIVILEGE       = -32504;
    public const INVALID_JSON_RPC_OBJECT      = -32600;
    public const METHOD_NOT_FOUND             = -32601;
    public const INVALID_AMOUNT               = -31001;
    public const TRANSACTION_NOT_FOUND        = -31003;
    public const INVALID_ACCOUNT              = -31050;
    public const COULD_NOT_CANCEL             = -31007;
    public const COULD_NOT_PERFORM            = -31008;
}
