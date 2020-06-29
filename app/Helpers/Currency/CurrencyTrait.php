<?php

namespace App\Helpers\Currency;

trait CurrencyTrait
{
    public function getCurrencyColumns(): array
    {
        return $this->currency_columns ?? ['price'];
    }

    public function toArray(): array
    {
        $this->currencyToArray();
        return parent::toArray();
    }

    public function currencyToArray(): void
    {
        foreach ($this->getCurrencyColumns() as $column) {
            if (isset($this->attributes[$column])) {
                $this->attributes[$column] = (float)$this->attributes[$column] * CurrencyHelper::getAmount();
            }
        }
    }
}
