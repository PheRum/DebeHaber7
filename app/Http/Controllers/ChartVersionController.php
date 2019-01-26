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
  public function index()
  {
    return view('chart/version/index');
  }


  public function get_chartversion($teamID)
  {
    $ChartVersion=ChartVersion::where('taxpayer_id',$teamID)->
    leftJoin('taxpayers', 'chart_versions.taxpayer_id', 'taxpayers.id')
    ->select('chart_versions.id','chart_versions.name'
    ,'chart_versions.taxpayer_id','taxpayers.name as taxpayer')
    ->get();
    return response()->json($ChartVersion);
  }


  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function create()
  {
    //
  }

  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {

    if ($request->id==0) {
      $ChartVersion= new ChartVersion();

    }
    else {
      $ChartVersion= ChartVersion::where('id',$request->id)->first();

    }

    $ChartVersion->name=$request->name;
    $ChartVersion->save();

    return response()->json('ok');

  }

  /**
  * Display the specified resource.
  *
  * @param  \App\ChartVersion  $chartVersion
  * @return \Illuminate\Http\Response
  */
  public function show(ChartVersion $chartVersion)
  {
    //
  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param  \App\ChartVersion  $chartVersion
  * @return \Illuminate\Http\Response
  */
  public function edit(ChartVersion $chartVersion)
  {
    //
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \App\ChartVersion  $chartVersion
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request, ChartVersion $chartVersion)
  {
    //
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
