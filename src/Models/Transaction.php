<?php

namespace JscorpTech\Payme\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use JscorpTech\Payme\Enums\ErrorEnum;
use JscorpTech\Payme\Exceptions\PaymeException;
use JscorpTech\Payme\Utils\Transaction as TransactionUtil;

class Transaction extends Model
{
    use HasFactory;
    use TransactionUtil;

    public $table = "payme_transactions";

    public $fillable = [
        "amount",
        "transaction_id",
        "time",
        "create_time",
        "perform_time",
        "cancel_time",
        "state",
        "reason",
        "order_id"
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Tranzaksiya transaction_id orqali olish.
     *
     * @param int $request_id So'rov identifikatori
     * @param string $id Transaction identifikatori
     * @return Transaction
     */
    public static function getTransaction($request_id, $id): self
    {
        $transaction = static::query()->where(['transaction_id' => $id])->latest()->first();
        if (!$transaction) {
            throw new PaymeException($request_id, 'Transaction not found', ErrorEnum::TRANSACTION_NOT_FOUND);
        }
        return $transaction;
    }
}
