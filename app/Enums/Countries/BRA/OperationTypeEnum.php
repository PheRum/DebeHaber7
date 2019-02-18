<?php

namespace App\Enums\BRA;

use Nasyrov\Laravel\Enums\Enum;

class OperationTypeEnum extends Enum
{
    // const DirectPurchase    = 0; Do not give this option in PRY. Make it default.
    const AgriculturalPurchase   = 6;
    const FiscalCreditDueExports = 7;
    const FiscalCredit           = 8;
    const SalesReturn            = 9;
    const FiscalCreditDueTaxpayer = 10;
    const VATExemptPurchases     = 11;

    public static function labels()
    {
        return static::constants()
        ->flip()
        ->map(function ($key) {
            // Place your translation strings in `resources/lang/en/enum.php`
            return trans(sprintf('enum.%s', strtolower($key)));
        })
        ->all();
    }
}
