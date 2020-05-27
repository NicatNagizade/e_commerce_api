<?php

namespace App\Helpers\Currency;

trait CurrencyTrait
{

    public $currency_columns = [];

    public function getCurrencyColumns(): array
    {
        return $this->currency_columns;
    }

    public function toArray(): array
    {
        if(!$this->currency_columns){
            $this->currencyToArray();
        }
        return parent::toArray();
    }

    public function currencyToArray(): void
    {
        foreach ($this->getCurrencyColumns() as $column) {
            if (isset($this->$column)) {
                $this->$column = (float)$this->$column * static::getCurrencyAmount();
            }
        }
    }

    public static function getCurrencyAmount(): float
    {
        return CurrencyHelper::getAmount();
    }
}
