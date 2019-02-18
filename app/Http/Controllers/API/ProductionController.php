<?php

namespace App\Http\Controller\API;

use App\Production;
use App\Taxpayer;
use App\Cycle;
use App\ChartAlias;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function start(Request $request)
    {
        //Convert data from
        $data = json_decode($request, true);
        //Process Transaction
        //
    }

    public function processTransaction($data)
    {
        //process transaction

        //process detail

        //return transaction saved status (ok or error).

    }

    public function processDetail($detail)
    {

    }

}
