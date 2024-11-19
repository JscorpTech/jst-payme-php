<?php

namespace JscorpTech\Payme\Exceptions;

class PaymeException extends \Exception
{
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
