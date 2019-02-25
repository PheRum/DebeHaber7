<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Chart;
use App\FixedAsset;
use App\Http\Resources\GeneralResource;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return GeneralResource::collection(
            FixedAsset::where('taxpayer_id', $taxPayer->id)
                ->with('chart')
                ->paginate(50)
        );
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request, Taxpayer $taxPayer, Cycle $cycle)
    {
        $fixedAsset = FixedAsset::firstOrNew('id', $request->id);
        $fixedAsset->chart_id = $request->chart_id;
        $fixedAsset->taxpayer_id = $taxPayer->id;
        $fixedAsset->currency_id = $request->currency_id;
        $fixedAsset->rate = $request->rate;
        $fixedAsset->serial = $request->serial;
        $fixedAsset->name = $request->name;
        $fixedAsset->purchase_date = $request->purchase_date;
        $fixedAsset->purchase_value = $request->purchase_value;
        $fixedAsset->current_value = $request->current_value;
        $fixedAsset->quantity = $request->quantity;
        $fixedAsset->save();

        return response()->json('Ok', 200);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\FixedAsset  $transaction
    * @return \Illuminate\Http\Response
    */
    public function show(Taxpayer $taxPayer, Cycle $cycle, $fixedAssetId)
    {
        return new GeneralResource(
            FixedAsset::where('id', $fixedAssetId)
                ->where('taxpayer_id', $taxPayer->id)
                ->with('chart')
                ->first()
        );
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\FixedAsset  $fixedAsset
    * @return \Illuminate\Http\Response
    */
    public function destroy(FixedAsset $fixedAsset)
    {
        $fixedAsset->delete();
    }

    public function depreciate(FixedAsset $fixedAsset)
    {
        $fixedAssetGroup = Chart::find($fixedAsset->chart_id);

        if (isset($fixedAssetGroup) && ($fixedAsset->purchase_value > 0) && ($fixedAssetGroup->asset_years > 0)) {
            // get the difference in date between now and the purchase date.
            $diffInDays = Carbon::now()->diffInDays($fixedAsset->purchase_date);
            // calculate in days.
            $dailyDepreciation = $fixedAsset->purchase_value / ($fixedAssetGroup->asset_years * 365);
            // use the difference in time to calculate percentage reduction from purchase value.
            $fixedAsset->currentValue = $fixedAsset->purchase_value - ($dailyDepreciation * $diffInDays);
            $fixedAsset->save();
        }
    }
}
