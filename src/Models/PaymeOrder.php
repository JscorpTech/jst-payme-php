<?php

namespace JscorpTech\Payme\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymeOrder extends Model
{
    use HasFactory;

    public $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = config("payme.order_table");
    }

    public $fillable = [
        "amount",
        "state",
    ];
}
