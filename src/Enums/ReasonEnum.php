<?php


namespace JscorpTech\Payme\Enums;


enum ReasonEnum: int
{
    case RECEIVERS_NOT_FOUND         = 1;
    case PROCESSING_EXECUTION_FAILED = 2;
    case EXECUTION_FAILED            = 3;
    case CANCELLED_BY_TIMEOUT        = 4;
    case FUND_RETURNED               = 5;
    case UNKNOWN                     = 10;
}
