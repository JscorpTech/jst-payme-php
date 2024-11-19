<?php


namespace JscorpTech\Payme\Enums;


enum TransactionEnum
{
    public const TIMEOUT = 43200000;

    public const STATE_CREATED                  = 1;
    public const STATE_COMPLETED                = 2;
    public const STATE_CANCELLED                = -1;
    public const STATE_CANCELLED_AFTER_COMPLETE = -2;

    public const REASON_RECEIVERS_NOT_FOUND         = 1;
    public const REASON_PROCESSING_EXECUTION_FAILED = 2;
    public const REASON_EXECUTION_FAILED            = 3;
    public const REASON_CANCELLED_BY_TIMEOUT        = 4;
    public const REASON_FUND_RETURNED               = 5;
    public const REASON_UNKNOWN                     = 10;
}
