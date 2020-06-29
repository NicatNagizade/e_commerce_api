<?php

namespace App\Helpers\Currency;

use Illuminate\Support\Facades\DB;

class CurrencyHelper
{
    private static $amount;
    private static $default = 'azn';
    private static $table = 'currencies';
    private static $header_name = 'Content-currency';

    public static function defaultCurrency()
    {
        return static::$default;
    }

    public static function getAmount(): float
    {
        if (!isset(static::$amount)) {
            static::$amount = static::getAmountValue();
        }
        return (float) static::$amount;
    }

    public static function getAmountValue(): float
    {
        $res = 1;
        if ($currency = request()->header(static::$header_name)) {
            if ($currency !== static::defaultCurrency()) {
                $amount = DB::table(static::$table)->where('name', $currency)->value('amount');
                if ($amount) {
                    $res = $amount;
                }
            }
        }
        return (float) $res;
    }
}
