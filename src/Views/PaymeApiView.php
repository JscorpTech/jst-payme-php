<?php

namespace JscorpTech\Payme\Views;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use JscorpTech\Payme\Enums\ErrorEnum;
use JscorpTech\Payme\Enums\StateEnum;
use JscorpTech\Payme\Utils\Merchant;
use JscorpTech\Payme\Exceptions\PaymeException;
use JscorpTech\Payme\Utils\Response as UtilsResponse;
use JscorpTech\Payme\Utils\Time;
use JscorpTech\Payme\Utils\Utils;

class PaymeApiView
{
    use UtilsResponse;


    public $merchant;
    private string $login;
    private string $key;
    public int $request_id;
    public string $method;
    public array $params;
    public int $time;
    public $order;
    public string $field;
    public $transaction;

    public function __construct(Request $request)
    {
        $this->request_id = $request->input("id");
        $this->login = config("payme.login");
        $this->key = config("payme.key");
        $this->merchant = new Merchant();
        $this->method = $request->input("method");
        $this->params = $request->input("params", []);
        $this->time = Time::get_time();
        $this->order = config("payme.order");
        $this->transaction = config("payme.transaction");
        $this->field = config("payme.field");
    }

    public function __invoke(Request $request)
    {
        try {
            $this->merchant->Authorize($this->request_id, $this->login, $this->key);

            switch ($this->method) {
                case "CheckPerformTransaction":
                    return $this->CheckPerformTransaction($request);
                case "CreateTransaction":
                    return $this->CreateTransaction($request);
                case "PerformTransaction":
                    return $this->PerformTransaction($request);
                case "CancelTransaction":
                    return $this->CancelTransaction($request);
                case "CheckTransaction":
                    return $this->CheckTransaction($request);
                case "GetStatement":
                    return $this->GetStatement($request);
                case "ChangePassword":
                    return $this->ChangePassword($request);
                default:
                    throw new PaymeException($this->request_id, "Method not found", ErrorEnum::METHOD_NOT_FOUND);
            }
        } catch (PaymeException $e) {
            return $this->error($e);
        } catch (\Exception $e) {
            return Response::json([
                "id" => $this->request_id,
                "result" => null,
                "error" => [
                    "code" => ErrorEnum::INTERNAL_SYSTEM,
                    "message" => $e->getMessage()
                ]
            ]);
        }
    }

    public function GetStatement()
    {
        $transactions = $this->transaction::query()->where("time", ">=", $this->params['from'])->where("time", "<=", $this->params['to'])->get();
        $statement = [];
        foreach ($transactions as $transaction) {
            $statement[] =  [
                "id"            => $transaction->transaction_id,
                "time"          => $transaction->time,
                "amount"        => $transaction->order->amount,
                "account"       => [
                    $this->field      => $transaction->order->id
                ],
                "create_time"   => $transaction->create_time,
                "perform_time"  => $transaction->create_time ?? 0,
                "cancel_time"   => $transaction->cancel_time ?? 0,
                "transaction"   => (string) $transaction->id,
                "state"         => $transaction->state,
                "reason"        => $transaction->reason,
            ];
        }
        return $this->success([
            "result" => [
                "transactions" => $statement
            ]
        ]);
    }
    /**
     * Parol o'zgartirish
     */
    public function ChangePassword()
    {
        // TODO: implement
        return $this->success([
            "detail" => "waiting!"
        ]);
    }

    /**
     * To'lovni bekor qilish
     */
    public function CancelTransaction()
    {
        $transaction = $this->transaction::getTransaction($this->request_id, $this->params['id']);
        if ($transaction->isCancel()) {
            return $this->success([
                "transaction" => (string) $transaction->id,
                "cancel_time" => $transaction->cancel_time,
                "state"       => $transaction->state,
            ]);
        }
        $transaction->state = $transaction->getCancelState();
        $transaction->cancel_time = $this->time;
        $transaction->reason = $this->params['reason'];
        Utils::callback(config("payme.cancel_callback"), $transaction);
        $transaction->save();
        return $this->success([
            "transaction" => (string) $transaction->id,
            "cancel_time" => $transaction->cancel_time,
            "state"       => $transaction->state,
        ]);
    }

    /**
     * To'lov yakunlandi
     */
    public function PerformTransaction()
    {
        $transaction = $this->transaction::getTransaction($this->request_id, $this->params['id']);
        if ($transaction->isComplete()) {
            return $this->success([
                "transaction"  => (string) $transaction->id,
                "perform_time" => $transaction->perform_time,
                "state"        => $transaction->state
            ]);
        }
        $transaction->state = StateEnum::COMPLETED;
        $transaction->perform_time = $this->time;
        Utils::callback(config("payme.success_callback"), $transaction);
        $transaction->save();
        return $this->success([
            "transaction"  => (string) $transaction->id,
            "perform_time" => $transaction->perform_time,
            "state"        => $transaction->state
        ]);
    }

    /**
     * To'lov yaratish mumkunmi?
     */
    public function CheckPerformTransaction()
    {
        $this->merchant->validateParams($this->request_id, $this->params);
        $order = $this->order::query()->where(['id' => $this->params['account'][$this->field]]);
        if (!$order->exists() or $order->first()->state) {
            throw new PaymeException($this->request_id, "Order not found", ErrorEnum::INVALID_ACCOUNT);
        }
        return $this->success(["allow" => true]);
    }

    /**
     * To'lov haqida malumo olish
     */
    public function CheckTransaction()
    {
        $transaction = $this->transaction::getTransaction($this->request_id, $this->params['id']);
        return $this->success([
            "create_time"  => $transaction->create_time,
            "perform_time" => $transaction->perform_time ?? 0,
            "cancel_time"  => $transaction->cancel_time ?? 0,
            "transaction"  => (string) $transaction->id,
            "state"        => $transaction->state,
            "reason"       => $transaction->reason
        ]);
    }

    /**
     * Yangi to'lov yaratish
     */
    public function CreateTransaction()
    {
        $this->merchant->validateParams($this->request_id, $this->params);
        $transaction = $this->transaction::query()->where(['order_id' => $this->params['account'][$this->field]])->latest()->first();
        if (config("payme.one_time_payment")) {
            $this->merchant->CheckTransaction($this->request_id, $transaction, $this->params['id']);
        }
        if (!$transaction or $transaction->isCancel() or $transaction->transaction_id != $this->params['id']) {
            $transaction = $this->transaction::query()->create(
                [
                    "transaction_id" => $this->params['id'],
                    "time"           => $this->params['time'],
                    "create_time"    => $this->time,
                    "amount"         => $this->params['amount'],
                    "order_id"       => $this->params['account'][$this->field],
                    "state"          => StateEnum::CREATED
                ]
            );
        }

        return $this->success([
            "create_time" => $transaction->create_time,
            "transaction" => (string) $transaction->id,
            "state"       => $transaction->state
        ]);
    }
}
