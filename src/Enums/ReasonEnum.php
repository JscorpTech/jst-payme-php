<?php


namespace JscorpTech\Payme\Enums;


class ReasonEnum
{
    const RECEIVERS_NOT_FOUND         = 1;
    const PROCESSING_EXECUTION_FAILED = 2;
    const EXECUTION_FAILED            = 3;
    const CANCELLED_BY_TIMEOUT        = 4;
    const FUND_RETURNED               = 5;
    const UNKNOWN                     = 10;
}
