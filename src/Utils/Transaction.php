<?php

namespace JscorpTech\Payme\Utils;

use JscorpTech\Payme\Enums\StateEnum;

trait Transaction
{
    /**
     * Tranzaksiya yakunlanganmi.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->state === StateEnum::COMPLETED;
    }

    /**
     * Tranzaksiya summasi to'g'ri ekanligini tekshirish.
     *
     * @param float $expectedAmount Kutilayotgan summa
     * @return bool
     */
    public function validateAmount(float $expectedAmount): bool
    {
        return $this->amount === $expectedAmount;
    }

    /**
     * Tranzaksiya bekor qilingan yoki yo'qligini bilish.
     *
     * @return bool
     */
    public function isCancel(): bool
    {
        return in_array($this->state, [
            StateEnum::CANCELLED,
            StateEnum::CANCELLED_AFTER_COMPLETE
        ]);
    }

    /**
     * Bekor qilinganda yangi stateni olish
     * 
     * @return int
     */
    public function getCancelState(): int{
        return match ($this->state) {
            StateEnum::CREATED => StateEnum::CANCELLED,
            StateEnum::COMPLETED => StateEnum::CANCELLED_AFTER_COMPLETE,
            default => $this->state
        };
    }

}
