<?php

namespace JscorpTech\Payme\Exceptions;

class PaymeException extends \Exception
{
    public const ERROR_INTERNAL_SYSTEM         = -32400;
    public const ERROR_INSUFFICIENT_PRIVILEGE  = -32504;
    public const ERROR_INVALID_JSON_RPC_OBJECT = -32600;
    public const ERROR_METHOD_NOT_FOUND        = -32601;
    public const ERROR_INVALID_AMOUNT          = -31001;
    public const ERROR_TRANSACTION_NOT_FOUND   = -31003;
    public const ERROR_INVALID_ACCOUNT         = -31050;
    public const ERROR_COULD_NOT_CANCEL        = -31007;
    public const ERROR_COULD_NOT_PERFORM       = -31008;

    public $request_id;
    public $error;
    public $data;

    /**
     * PaycomException constructor.
     * @param int $request_id id of the request.
     * @param string|array $message error message.
     * @param int $code error code.
     * @param string|null $data parameter name, that resulted to this error.
     */
    public function __construct($request_id, $message, $code, $data = null)
    {
        $this->request_id = $request_id;
        $this->message    = $message;
        $this->code       = $code;
        $this->data       = $data;

        // prepare error data
        $this->error = ['code' => $this->code];

        if ($this->message) {
            $this->error['message'] = $this->message;
        }

        if ($this->data) {
            $this->error['data'] = $this->data;
        }
    }
}
