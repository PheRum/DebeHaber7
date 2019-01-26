<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Chart;
use App\FixedAsset;
use App\Http\Resources\FixedAssetResource;
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
        $charts = Chart::FixedAssetGroups()->get();
        return view('commercial/fixed-assets')->with('charts', $charts);
    }

    public function getFixedAsset(Taxpayer $taxPayer, Cycle $cycle)
    {
        return FixedAssetResource::collection(FixedAsset::where('taxpayer_id', $taxPayer->id)->with('chart')->paginate(50));
    }

    public function getFixedAssetByID(Taxpayer $taxPayer, Cycle $cycle,$id)
    {
        $fixedasset = FixedAsset::with('chart')
        ->where('id',$id)
        ->get();

        return response()->json($fixedasset);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request, Taxpayer $taxPayer, Cycle $cycle)
    {
        $fixedasset = $request->id == 0 ? new FixedAsset() : FixedAsset::where('id', $request->id)->first();
        $fixedasset->chart_id = $request->chart_id;
        $fixedasset->taxpayer_id = $taxPayer->id;
        $fixedasset->currency_id = $request->currency_id;
        $fixedasset->rate = $request->rate;
        $fixedasset->serial = $request->serial;
        $fixedasset->name = $request->name;
        $fixedasset->purchase_date = $request->purchase_date;
        $fixedasset->purchase_value = $request->purchase_value;
        $fixedasset->current_value = $request->current_value;
        $fixedasset->quantity = $request->quantity;
        $fixedasset->save();

        return response()->json('Ok', 200);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\FixedAsset  $fixedAsset
    * @return \Illuminate\Http\Response
    */
    public function edit(FixedAsset $fixedAsset)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\FixedAsset  $fixedAsset
    * @return \Illuminate\Http\Response
    */
    public function destroy(FixedAsset $fixedAsset)
    {
        //
    }
}
