<?php

namespace JscorpTech\Payme\Utils;

use JscorpTech\Payme\Enums\TransactionEnum;
use JscorpTech\Payme\Exceptions\PaymeException;
use JscorpTech\Payme\Models\Order;

class Merchant
{
    public function Authorize($request_id, $login, $key)
    {
        $headers = getallheaders();

        if (
            !$headers || !isset($headers['Authorization']) ||
            !preg_match('/^\s*Basic\s+(\S+)\s*$/i', $headers['Authorization'], $matches) ||
            base64_decode($matches[1]) != $login . ":" . $key
        ) {
            throw new PaymeException(
                $request_id,
                'Insufficient privilege to perform this method.',
                TransactionEnum::ERROR_INSUFFICIENT_PRIVILEGE
            );
        }

        return true;
    }

    public function CheckTransaction(int $request_id, $transaction, $transaction_id)
    {
        if (
            $transaction and
            ($transaction->state == TransactionEnum::STATE_CREATED or
                $transaction->state == TransactionEnum::STATE_COMPLETED) and
            $transaction->transaction_id != $transaction_id
        ) {
            throw new PaymeException($request_id, "Unfinished transaction available", TransactionEnum::ERROR_INVALID_ACCOUNT);
        }
    }


    /**
     * Paymedan kelgan malumotlarni tekshirish uchun
     * 
     * @return bool
     */
    public function validateParams($request_id, $params): bool
    {
        if (!isset($params['account']['order_id'])) {
            throw new PaymeException($request_id, 'Order ID is required', TransactionEnum::ERROR_INVALID_ACCOUNT);
        }
        if (!isset($params['amount'])) {
            throw new PaymeException($request_id, 'Amount is required', TransactionEnum::ERROR_INVALID_AMOUNT);
        }
        $orders = Order::query()->where(['id' => $params['account']['order_id']]);
        if (!$orders->exists()) {
            throw new PaymeException($request_id, 'Order not found', TransactionEnum::ERROR_INVALID_ACCOUNT);
        }
        $order = $orders->first();
        if ($order->amount != $params['amount']) {
            throw new PaymeException($request_id, 'Amount mismatch', TransactionEnum::ERROR_INVALID_AMOUNT);
        }
        return true;
    }
}
