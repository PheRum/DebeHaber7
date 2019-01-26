<?php

namespace App\Http\Controllers;

use App\ChartVersion;
use Illuminate\Http\Request;

class ChartVersionController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index($teamID)
    {
        $chartVersion = ChartVersion::where('taxpayer_id', $teamID)
        ->leftJoin('taxpayers', 'chart_versions.taxpayer_id', 'taxpayers.id')
        ->select('chart_versions.id',
        'chart_versions.name',
        'chart_versions.taxpayer_id',
        'taxpayers.name as taxpayer')
        ->get();

        return response()->json($chartVersion);
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
