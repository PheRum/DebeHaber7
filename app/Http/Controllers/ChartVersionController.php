<?php

namespace App\Http\Controllers;

use App\ChartVersion;
use Illuminate\Http\Request;
use App\Http\Resources\GeneralResource;

class ChartVersionController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer)
    {
        return GeneralResource::collection(
            ChartVersion::where(function($q) use ($taxPayer) {
                return $q->where('taxpayer_id', $taxPayer->id)
                ->orWhereNull('taxpayer_id')
                ->where('country', $taxPayer->country);
            })->paginate(50)
        );
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $chartVersion = $request->id == 0 ? new ChartVersion() : ChartVersion::where('id', $request->id)->first();
        $chartVersion->name = $request->name;
        $chartVersion->save();

        return response()->json('ok');
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\ChartVersion  $chartVersion
    * @return \Illuminate\Http\Response
    */
    public function destroy(ChartVersion $chartVersion)
    {
        //
    }
}
