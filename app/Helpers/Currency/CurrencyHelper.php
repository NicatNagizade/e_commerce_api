<?php

namespace App\Helpers\Currency;

use Illuminate\Support\Facades\DB;

class CurrencyHelper
{
    private static $amount;
    private static $default = 'azn';
    private static $table = 'currencies';

    public static function getAmount(): float
    {
        if (!isset(static::$amount)) {
            if ($header = request()->header('Content-currency')) {
                if ($header != static::$default) {
                    $amount = DB::table(static::$table)->where('name', $header)->value('amount');
                    if ($amount) {
                        static::$amount = $amount;
                    }
                }
            }
            if (!isset(static::$amount)) {
                static::$amount = 1;
            }
        }
        return (float) static::$amount;
    }
}
