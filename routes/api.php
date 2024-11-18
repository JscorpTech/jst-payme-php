<?php

use Illuminate\Support\Facades\Route;
use JscorpTech\Payme\Views\PaymeApiView;

Route::prefix("payment")->group(function () {
    Route::post("payme/merchant/", PaymeApiView::class);
});
