<?php

namespace App\Enums;

use Nasyrov\Laravel\Enums\Enum;

class ChartTypeEnum extends Enum
{
    const Assets            = 1;
    const Liabilities       = 2;
    const Equity            = 3;
    const Revenues          = 4;
    const Expenses          = 5;

    public function getStatusAttribute($attribute) {
        return new ChartTypeEnum($attribute);
    }

    public function setStatusAttribute(UserStatusEnum $attribute) {
        $this->attributes['status'] = $attribute->getValue();
    }

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
