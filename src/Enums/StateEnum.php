<?php


namespace JscorpTech\Payme\Enums;


class StateEnum
{
    const CREATED                      = 1;
    const COMPLETED                    = 2;
    const CANCELLED                    = -1;
    const CANCELLED_AFTER_COMPLETE     = -2;
}
