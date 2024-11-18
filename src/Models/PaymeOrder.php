<?php


namespace Jscorptech\Payme\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PaymeOrder extends Model{
    use HasFactory;

    public $table = "payme_orders";

    public $fillable = [
        "amount",
        "state",
    ];


}