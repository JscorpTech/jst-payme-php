<?php

namespace JscorpTech\Payme\Utils;

use JscorpTech\Payme\Enums\ErrorEnum;
use JscorpTech\Payme\Enums\StateEnum;
use JscorpTech\Payme\Exceptions\PaymeException;

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
                ErrorEnum::INSUFFICIENT_PRIVILEGE
            );
        }

        return true;
    }

    public function CheckTransaction(int $request_id, $transaction, $transaction_id)
    {
        if (
            $transaction and
            ($transaction->state == StateEnum::CREATED or
                $transaction->state == StateEnum::COMPLETED) and
            $transaction->transaction_id != $transaction_id
        ) {
            throw new PaymeException($request_id, "Unfinished transaction available", ErrorEnum::INVALID_ACCOUNT);
        }
    }


    /**
     * Paymedan kelgan malumotlarni tekshirish uchun
     * 
     * @return bool
     */
    public function validateParams($request_id, $params): bool
    {
        if (!isset($params['account'][config("payme.field")])) {
            throw new PaymeException($request_id, 'Order ID is required', ErrorEnum::INVALID_ACCOUNT);
        }
        if (!isset($params['amount'])) {
            throw new PaymeException($request_id, 'Amount is required', ErrorEnum::INVALID_AMOUNT);
        }
        $orders = config("payme.order")::query()->where(['id' => $params['account'][config("payme.field")]]);
        if (!$orders->exists()) {
            throw new PaymeException($request_id, 'Order not found', ErrorEnum::INVALID_ACCOUNT);
        }
        $order = $orders->first();
        if ($order->{config("payme.amount")} != $params['amount']) {
            throw new PaymeException($request_id, 'Amount mismatch', ErrorEnum::INVALID_AMOUNT);
        }
        return true;
    }
}
