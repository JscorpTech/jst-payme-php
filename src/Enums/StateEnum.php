<?php


namespace JscorpTech\Payme\Enums;


class StateEnum
{
    public const CREATED                      = 1;
    public const COMPLETED                    = 2;
    public const CANCELLED                    = -1;
    public const CANCELLED_AFTER_COMPLETE     = -2;
}
