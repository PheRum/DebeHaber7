<?php

namespace App\Enums;

use Nasyrov\Laravel\Enums\Enum;

class TaxpayerIntegrationEnum extends Enum
{
    const Taxpayer      = 1;
    const Accountant    = 2;
    const Auditor       = 3;

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
