<?php


namespace JscorpTech\Payme\Enums;


enum StateEnum: int
{
    case CREATED                      = 1;
    case COMPLETED                    = 2;
    case CANCELLED                    = -1;
    case CANCELLED_AFTER_COMPLETE     = -2;
}
