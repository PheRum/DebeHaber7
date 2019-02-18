<?php

namespace App\Enums;

use Nasyrov\Laravel\Enums\Enum;

class ChartRevenueTypeEnum extends Enum
{
    const Revenue = 1;
    const SalesReturns = 2; //Contra Account
    const DiffInExchangeRate = 3;
    const RevenuFromInventory = 4;

    public static function labels()
    {
        return static::constants()
        ->flip()
        ->map(function ($key) {
            // Place your translation strings in `resources/lang/en/enum.php`
            return trans(sprintf('enum.%s', $key));
        })
        ->all();
    }
}
