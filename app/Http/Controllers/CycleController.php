<?php

namespace App\Http\Controllers;

use App\ChartVersion;
use App\Taxpayer;
use App\Cycle;
use App\Chart;
use App\CycleBudget;
use App\JournalDetail;
use Illuminate\Http\Request;
use App\Http\Resources\GeneralResource;
use DB;

class CycleController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return GeneralResource::collection(
            Cycle::with('chartVersion')
            ->orderBy('year', 'desc')
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
        $cycle = $request->id != 0 ? Cycle::find($request->id) : $cycle = new Cycle();

        if ($cycle != null)
        {
            $cycle->taxpayer_id = $taxPayer->id;
            $cycle->chart_version_id = $request->chart_version_id;
            $cycle->year = $request->year;
            $cycle->start_date = $request->start_date;
            $cycle->end_date = $request->end_date;
            $cycle->save();

            return response()->json('ok', 200);
        }
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Cycle  $cycleId
    * @return \Illuminate\Http\Response
    */
    public function show(Taxpayer $taxPayer,Cycle $cycle, $cycleId)
    {
        return new GeneralResource(
            Cycle::where('id', $cycleId)->first()
        );
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Cycle  $cycle
    * @return \Illuminate\Http\Response
    */
    public function destroy(Cycle $cycle)
    {
        //
    }
}
