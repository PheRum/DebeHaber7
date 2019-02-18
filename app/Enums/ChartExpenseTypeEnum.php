<?php

namespace App\Enums;

use Nasyrov\Laravel\Enums\Enum;

class ChartExpenseTypeEnum extends Enum
{
    const CostofGoodsSold       = 1;
    const AdvertisingExpense    = 2;
    const FinancialFees         = 3;
    const DepreciationExpense   = 4;
    const PayrollTaxExpense     = 5;
    const RentExpense           = 6;
    const SuppliesExpense       = 7;
    const UtilitiesExpense      = 8;
    const WagesExpense          = 9;
    const OtherExpenses         = 10;
    const DiffInExchangeRate    = 11;

    public static function labels()
    {
        return static::constants()
        ->flip()
        ->map(function ($key)
        {
            return trans(sprintf('enum.%s', $key));
        })
        ->all();
    }
}
