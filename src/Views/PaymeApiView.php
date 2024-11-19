<?php

namespace JscorpTech\Payme\Views;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use JscorpTech\Payme\Enums\TransactionEnum;
use JscorpTech\Payme\Utils\Merchant;
use JscorpTech\Payme\Exceptions\PaymeException;
use JscorpTech\Payme\Models\Order;
use JscorpTech\Payme\Models\Transaction;
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

    public function __construct(Request $request)
    {
        $this->request_id = $request->input("id");
        $this->login = config("payme.login");
        $this->key = config("payme.key");
        $this->merchant = new Merchant();
        $this->method = $request->input("method");
        $this->params = $request->input("params", []);
        $this->time = Time::get_time();
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
                    throw new PaymeException($this->request_id, "Method not found", TransactionEnum::ERROR_METHOD_NOT_FOUND);
            }
        } catch (PaymeException $e) {
            return $this->error($e);
        } catch (\Exception $e) {
            return Response::json([
                "id" => $this->request_id,
                "result" => null,
                "error" => [
                    "code" => TransactionEnum::ERROR_INTERNAL_SYSTEM,
                    "message" => $e->getMessage()
                ]
            ]);
        }
    }

    public function GetStatement()
    {
        $transactions = Transaction::query()->where("time", ">=", $this->params['from'])->where("time", "<=", $this->params['to'])->get();
        $statement = [];
        foreach ($transactions as $transaction) {
            $statement[] =  [
                "id"            => $transaction->transaction_id,
                "time"          => $transaction->time,
                "amount"        => $transaction->order->amount,
                "account"       => [
                    "order_id"      => $transaction->order->id
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
    public function ChangePassword()
    {
        return $this->success([
            "detail" => "waiting!"
        ]);
    }

    public function CancelTransaction()
    {
        $transaction = Transaction::getTransaction($this->request_id, $this->params['id']);
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
        Utils::callback(config("payme.cancel_callback"));
        $transaction->save();
        return $this->success([
            "transaction" => (string) $transaction->id,
            "cancel_time" => $transaction->cancel_time,
            "state"       => $transaction->state,
        ]);
    }

    public function PerformTransaction()
    {
        $transaction = Transaction::getTransaction($this->request_id, $this->params['id']);
        if ($transaction->isComplete()) {
            return $this->success([
                "transaction"  => (string) $transaction->id,
                "perform_time" => $transaction->perform_time,
                "state"        => $transaction->state
            ]);
        }
        $transaction->state = TransactionEnum::STATE_COMPLETED;
        $transaction->perform_time = $this->time;
        Utils::callback(config("payme.success_callback"));
        $transaction->save();
        return $this->success([
            "transaction"  => (string) $transaction->id,
            "perform_time" => $transaction->perform_time,
            "state"        => $transaction->state
        ]);
    }

    public function CheckPerformTransaction()
    {
        $this->merchant->validateParams($this->request_id, $this->params);
        $order = Order::query()->where(['id' => $this->params['account']['order_id']]);
        if (!$order->exists() or $order->first()->state) {
            throw new PaymeException($this->request_id, "Order not found", TransactionEnum::ERROR_INVALID_ACCOUNT);
        }
        return $this->success(["allow" => true]);
    }

    public function CheckTransaction()
    {
        $transaction = Transaction::getTransaction($this->request_id, $this->params['id']);
        return $this->success([
            "create_time"  => $transaction->create_time,
            "perform_time" => $transaction->perform_time ?? 0,
            "cancel_time"  => $transaction->cancel_time ?? 0,
            "transaction"  => (string) $transaction->id,
            "state"        => $transaction->state,
            "reason"       => $transaction->reason
        ]);
    }

    public function CreateTransaction()
    {
        $this->merchant->validateParams($this->request_id, $this->params);
        $time = Time::get_time();
        $transaction = Transaction::query()->where(['order_id' => $this->params['account']['order_id']])->latest()->first();
        $this->merchant->CheckTransaction($this->request_id, $transaction, $this->params['id']);

        if (!$transaction or $transaction->isCancel()) {
            $transaction = Transaction::query()->create(
                [
                    "transaction_id" => $this->params['id'],
                    "time"           => $this->params['time'],
                    "create_time"    => $time,
                    "order_id"       => $this->params['account']['order_id'],
                    "state"          => TransactionEnum::STATE_CREATED
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
