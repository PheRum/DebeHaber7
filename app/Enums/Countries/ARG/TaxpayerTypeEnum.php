<?php

namespace App\Enums\ARG;

use Nasyrov\Laravel\Enums\Enum;

class TaxpayerTypeEnum extends Enum
{
    const IRACIS   = 1;
    const IVA      = 2;
    const IRAGRO   = 3;
    const IRP      = 4;

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
