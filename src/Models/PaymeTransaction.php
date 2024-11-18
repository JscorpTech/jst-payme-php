<?php


namespace Jscorptech\Payme\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymeTransaction extends Model
{
    use HasFactory;

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
}
