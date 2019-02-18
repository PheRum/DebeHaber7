<?php

namespace App\Enums;

use Nasyrov\Laravel\Enums\Enum;

class ChartEquityTypeEnum extends Enum
{
    const CommonStock       = 1;
    const PreferredStock    = 2;
    const RetainedEarnings  = 3;
        
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
