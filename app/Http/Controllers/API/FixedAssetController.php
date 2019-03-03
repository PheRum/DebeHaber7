<?php

namespace App\Http\Controllers\API;

use App\FixedAsset;
use App\Taxpayer;
use App\Cycle;
use App\Http\Controllers\ChartController;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    public function start(Request $request)
    {
        $movementData = array();
        $cycle = null;

        //Process Transaction by 100 to speed up but not overload.
        for ($i = 0; $i < 100; $i++) {
            $chunkedData = $request[$i];

            if (isset($chunkedData)) {
                $taxPayer = $this->checkTaxPayer($chunkedData['TaxpayerTaxID'], $chunkedData['TaxpayerName']);

                //check and create cycle
                $firstDate = Carbon::now();
                $cycle = Cycle::My($taxPayer, $firstDate)->first();

                if (!isset($cycle)) {
                    $cycle = $this->checkCycle($taxPayer, $firstDate);
                }

                try {
                    $fixedAsset = $this->insertFixedAsset($chunkedData, $taxPayer, $cycle);
                    $movementData[$i] = $fixedAsset->id;
                } catch (\Exception $e) {
                    //Write items that don't insert into a variable and send back to ERP.
                    //Do Nothing
                }
            }
        }

        $assets = FixedAsset::whereIn('id', $movementData)->with('chart')->get();
        return response()->json($assets);
    }

    public function insertFixedAsset($data, Taxpayer $taxPayer, Cycle $cycle)
    {

        $fixedAsset = FixedAsset::where('id', $data['id'])->where('taxpayer_id', $taxPayer->id)->first() ?? new FixedAsset();

        $ChartController = new ChartController();

        $fixedAsset->id = $data['id'];
        $fixedAsset->chart_id = $ChartController->createIfNotExists_FixedAsset($taxPayer, $cycle, $data['LifeSpan'], $data['AssetGroup'])->id;
        $fixedAsset->taxpayer_id = $taxPayer->id;
        $fixedAsset->currency = $data['CurrencyCode'];
        $fixedAsset->rate = $data['CurrencyRate']; //TODO, add into ERP.
        $fixedAsset->serial = $data['ItemCode'];
        $fixedAsset->name = $data['ItemName'];
        $fixedAsset->purchase_date = $this->convert_date($data['PurchaseDate']);
        $fixedAsset->purchase_value = $data['PurchaseValue'];
        $fixedAsset->quantity = $data['Quantity'];

        //Take todays date to keep track of how new data really is.
        $fixedAsset->sync_date = Carbon::now();
        try {
            $fixedAsset->save();
        } catch (\Exception $e) {
            dd($e);
        }

        //Return account movement if not null.
        return $fixedAsset;
    }
}
