<?php

namespace App\Http\Controllers\API\PRY;

use App\TaxpayerIntegration;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function calculateTaxCode($taxID)
    {
        $base_max = 11;
        $arrayTaxID = str_split($taxID);
        $n = count($arrayTaxID);

        $suma = 0;
        $k = 2;

        for ($i = $n - 1; $i >= 0; $i--) {
            if (is_numeric($arrayTaxID[$i])) {
                $k = $k > $base_max ? 2 : $k;
                $suma += ($arrayTaxID[$i] * $k++);
            }
        }

        $v_resto = $suma % 11;
        $code = $v_resto > 1 ? 11 - $v_resto : 0;
        return $code;
    }
}
