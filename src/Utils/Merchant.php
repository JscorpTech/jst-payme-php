<?php

namespace JscorpTech\Payme\Utils;

use JscorpTech\Payme\Exceptions\PaymeException;
use JscorpTech\Payme\Models\PaymeTransaction;

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
                PaymeException::ERROR_INSUFFICIENT_PRIVILEGE
            );
        }

        return true;
    }

    public function getTransaction($request_id, $id)
    {
        $transaction = PaymeTransaction::query()->where(['transaction_id' => $id])->first();
        if (!$transaction) {
            throw new PaymeException($request_id, 'Transaction not found', PaymeException::ERROR_TRANSACTION_NOT_FOUND);
        }
        return $transaction;
    }

    public function CheckTransaction(int $request_id, $transaction, $transaction_id)
    {
        if (
            $transaction and
            ($transaction->state == PaymeTransaction::STATE_CREATED or
                $transaction->state == PaymeTransaction::STATE_COMPLETED) and
            $transaction->transaction_id != $transaction_id
        ) {
            throw new PaymeException($request_id, "Unfinished transaction available", PaymeException::ERROR_INVALID_ACCOUNT);
        }
    }
}
