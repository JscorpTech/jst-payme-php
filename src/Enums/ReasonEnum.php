<?php


namespace JscorpTech\Payme\Enums;


class ReasonEnum
{
    public const RECEIVERS_NOT_FOUND         = 1;
    public const PROCESSING_EXECUTION_FAILED = 2;
    public const EXECUTION_FAILED            = 3;
    public const CANCELLED_BY_TIMEOUT        = 4;
    public const FUND_RETURNED               = 5;
    public const UNKNOWN                     = 10;
}
