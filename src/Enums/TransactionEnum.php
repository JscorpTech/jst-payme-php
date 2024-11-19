<?php


namespace JscorpTech\Payme\Enums;


enum TransactionEnum: int
{
    case TIMEOUT = 43200000;

    case STATE_CREATED                  = 1;
    case STATE_COMPLETED                = 2;
    case STATE_CANCELLED                = -1;
    case STATE_CANCELLED_AFTER_COMPLETE = -2;

    case REASON_RECEIVERS_NOT_FOUND         = 1;
    case REASON_PROCESSING_EXECUTION_FAILED = 2;
    case REASON_EXECUTION_FAILED            = 3;
    case REASON_CANCELLED_BY_TIMEOUT        = 4;
    case REASON_FUND_RETURNED               = 5;
    case REASON_UNKNOWN                     = 10;

    case ERROR_INTERNAL_SYSTEM         = -32400;
    case ERROR_INSUFFICIENT_PRIVILEGE  = -32504;
    case ERROR_INVALID_JSON_RPC_OBJECT = -32600;
    case ERROR_METHOD_NOT_FOUND        = -32601;
    case ERROR_INVALID_AMOUNT          = -31001;
    case ERROR_TRANSACTION_NOT_FOUND   = -31003;
    case ERROR_INVALID_ACCOUNT         = -31050;
    case ERROR_COULD_NOT_CANCEL        = -31007;
    case ERROR_COULD_NOT_PERFORM       = -31008;
}
