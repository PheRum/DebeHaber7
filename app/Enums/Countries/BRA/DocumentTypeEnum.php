<?php

namespace App\Enums\BRA;

use Nasyrov\Laravel\Enums\Enum;

class DocumentTypeEnum extends Enum
{
    const Invoice           = 1;
    const DebitNote         = 2;
    const CreditNote        = 3;
    const CustomsClearence  = 4;
    const SelfInvoice       = 5;
    const Ticket            = 6;
    const AirTicket         = 7;
    const InvoiceFromAbroad = 8;
    const AbsorbedRetention = 9;
    const ElectronicAirTicket = 10;

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
