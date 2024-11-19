<?php

namespace JscorpTech\Payme\Utils;

use JscorpTech\Payme\Enums\TransactionEnum;

trait Transaction
{
    /**
     * Tranzaksiya yakunlanganmi.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->state === TransactionEnum::STATE_COMPLETED;
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
            TransactionEnum::STATE_CANCELLED,
            TransactionEnum::STATE_CANCELLED_AFTER_COMPLETE
        ]);
    }

    /**
     * Bekor qilinganda yangi stateni olish
     * 
     * @return int
     */
    public function getCancelState(): int{
        return match ($this->state) {
            TransactionEnum::STATE_CREATED => TransactionEnum::STATE_CANCELLED,
            TransactionEnum::STATE_COMPLETED => TransactionEnum::STATE_CANCELLED_AFTER_COMPLETE,
            default => $this->state
        };
    }

}
