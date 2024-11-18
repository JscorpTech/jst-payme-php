<?php


namespace JscorpTech\Payme\Utils;

use JscorpTech\Payme\Exceptions\PaymeException;
use JscorpTech\Payme\Models\PaymeOrder;

trait Validator
{
    public function validate($request_id, $params)
    {
        if (!isset($params['account']['order_id'])) {
            throw new PaymeException($request_id, 'Order ID is required', PaymeException::ERROR_INVALID_ACCOUNT);
        }
        if (!isset($params['amount'])) {
            throw new PaymeException($request_id, 'Amount is required', PaymeException::ERROR_INVALID_AMOUNT);
        }
        $orders = PaymeOrder::query()->where(['id' => $params['account']['order_id']]);
        if (!$orders->exists()) {
            throw new PaymeException($request_id, 'Order not found', PaymeException::ERROR_INVALID_ACCOUNT);
        }
        $order = $orders->first();
        if ($order->amount != $params['amount']) {
            throw new PaymeException($request_id, 'Amount mismatch', PaymeException::ERROR_INVALID_AMOUNT);
        }
        return true;
    }
}
