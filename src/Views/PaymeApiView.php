<?php

namespace JscorpTech\Payme\Views;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use JscorpTech\Payme\Utils\Merchant;
use JscorpTech\Payme\Exceptions\PaymeException;
use JscorpTech\Payme\Models\PaymeOrder;
use JscorpTech\Payme\Models\PaymeTransaction;
use JscorpTech\Payme\Utils\Response as UtilsResponse;
use JscorpTech\Payme\Utils\Validator;
use JscorpTech\Payme\Utils\Time;

class PaymeApiView
{
    use Validator, UtilsResponse;
    public $merchant;
    private string $login;
    private string $key;
    public int $request_id;
    public string $method;
    public array $params;

    public function __construct(Request $request)
    {
        $this->request_id = $request->input("id");
        $this->login = config("payme.login");
        $this->key = config("payme.key");
        $this->merchant = new Merchant();
        $this->method = $request->input("method");
        $this->params = $request->input("params", []);
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
                    throw new PaymeException($this->request_id, "Method not found", PaymeException::ERROR_METHOD_NOT_FOUND);
            }
        } catch (PaymeException $e) {
            return $this->error($e);
        } catch (\Exception $e) {
            return Response::json([
                "id" => $this->request_id,
                "result" => null,
                "error" => [
                    "code" => PaymeException::ERROR_INTERNAL_SYSTEM,
                    "message" => $e->getMessage()
                ]
            ]);
        }
    }

    public function GetStatement()
    {
        return $this->success([
            "detail" => "waiting!"
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
        $transaction = $this->merchant->getTransaction($this->request_id, $this->params['id']);
        if ($transaction->state == PaymeTransaction::STATE_COMPLETED) {
            return $this->success([
                "transaction" => (string) $transaction->id,
                "cancel_time" => $transaction->cancel_time,
                "state" => $transaction->state,
            ]);
        }
        $time = Time::get_time();
        $state = match ($transaction->state) {
            PaymeTransaction::STATE_CREATED => PaymeTransaction::STATE_CANCELLED,
            PaymeTransaction::STATE_COMPLETED => PaymeTransaction::STATE_CANCELLED_AFTER_COMPLETE,
            default => $transaction->state
        };
        $transaction->state = $state;
        $transaction->cancel_time = $time;
        $transaction->reason = $this->params['reason'];
        $callback = config("payme.cancel_callback");
        App::make($callback[0])->$callback[1]();
        $transaction->save();
        return $this->success([
            "transaction" => (string) $transaction->id,
            "cancel_time" => $transaction->cancel_time,
            "state" => $transaction->state,
        ]);
    }

    public function PerformTransaction()
    {
        $time = Time::get_time();
        $transaction = $this->merchant->getTransaction($this->request_id, $this->params['id']);
        if ($transaction->state == PaymeTransaction::STATE_COMPLETED) {
            return $this->success([
                "transaction" => (string) $transaction->id,
                "perform_time" => $transaction->perform_time,
                "state" => $transaction->state
            ]);
        }
        $transaction->state = PaymeTransaction::STATE_COMPLETED;
        $transaction->perform_time = $time;
        $callback = config("payme.success_callback");
        App::make($callback[0])->$callback[1]();
        $transaction->save();
        return $this->success([
            "transaction" => (string) $transaction->id,
            "perform_time" => $transaction->perform_time,
            "state" => $transaction->state
        ]);
    }

    public function CheckPerformTransaction()
    {
        $this->validate($this->request_id, $this->params);
        $order = PaymeOrder::query()->where(['id' => $this->params['account']['order_id']]);
        if (!$order->exists() or $order->first()->state) {
            throw new PaymeException($this->request_id, "Order not found", PaymeException::ERROR_INVALID_ACCOUNT);
        }
        return $this->success(["allow" => true]);
    }

    public function CheckTransaction()
    {
        $transaction = $this->merchant->getTransaction($this->request_id, $this->params['id']);
        return $this->success([
            "create_time" => $transaction->create_time,
            "perform_time" => $transaction->perform_time ?? 0,
            "cancel_time" => $transaction->cancel_time ?? 0,
            "transaction" => (string) $transaction->id,
            "state" => $transaction->state,
            "reason" => $transaction->reason
        ]);
    }

    public function CreateTransaction()
    {
        $this->validate($this->request_id, $this->params);
        $time = Time::get_time();
        $transaction = PaymeTransaction::query()->where(['order_id' => $this->params['account']['order_id']])->latest()->first();
        $this->merchant->CheckTransaction($this->request_id, $transaction, $this->params['id']);

        if (
            !$transaction or
            ($transaction->state == PaymeTransaction::STATE_CANCELLED or
                $transaction->state == PaymeTransaction::STATE_CANCELLED_AFTER_COMPLETE)
        ) {
            $transaction = PaymeTransaction::query()->create(
                [
                    "transaction_id" => $this->params['id'],
                    "time" => $this->params['time'],
                    "create_time" => $time,
                    "order_id" => $this->params['account']['order_id'],
                    "state" => PaymeTransaction::STATE_CREATED
                ]
            );
        }

        return $this->success([
            "create_time" => $transaction->create_time,
            "transaction" => (string) $transaction->id,
            "state" => $transaction->state
        ]);
    }
}
