<?php

namespace JscorpTech\Payme\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymeTransaction extends Model
{
    use HasFactory;

    public const TIMEOUT = 43200000;

    public const STATE_CREATED                  = 1;
    public const STATE_COMPLETED                = 2;
    public const STATE_CANCELLED                = -1;
    public const STATE_CANCELLED_AFTER_COMPLETE = -2;

    public const REASON_RECEIVERS_NOT_FOUND         = 1;
    public const REASON_PROCESSING_EXECUTION_FAILED = 2;
    public const REASON_EXECUTION_FAILED            = 3;
    public const REASON_CANCELLED_BY_TIMEOUT        = 4;
    public const REASON_FUND_RETURNED               = 5;
    public const REASON_UNKNOWN                     = 10;

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
        return $this->belongsTo(PaymeOrder::class);
    }
}
