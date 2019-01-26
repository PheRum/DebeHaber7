<?php

namespace App\Http\Controllers;

use App\CycleBudget;
use App\Taxpayer;
use App\Cycle;
use Illuminate\Http\Request;
use DB;

class CycleBudgetController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        //
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
    public function store(Request $request, Taxpayer $taxPayer, Cycle $cycle)
    {
        $charts = collect($request);

        foreach ($charts->where('is_accountable', true) as $detail)
        {
            //Make sure that atleast Debit OR Credit is more than zero to avoid
            if ($detail['debit'] > 0 || $detail['credit'] > 0)
            {
                $cyclebudget = CycleBudget::where('chart_id', $detail['chart_id'])->where('cycle_id', $cycle->id)->first() ?? new CycleBudget();

                $cyclebudget->cycle_id = $cycle->id;
                $cyclebudget->chart_id = $detail['id'];
                $cyclebudget->debit = $detail['debit'];
                $cyclebudget->credit = $detail['credit'];
                $cyclebudget->comment = $cycle->year . ' - ' . __('accounting.AccountingBudget');
                $cyclebudget->save();
            }
        }

        return response()->json('Ok', 200);
    }

    public function getCycleBudgetsByCycleID (Request $request, Taxpayer $taxPayer, Cycle $cycle, $cycleID)
    {
        $cyclebudget = CycleBudget::join('charts', 'cycle_budgets.chart_id', 'charts.id')
        ->where('cycle_id', $cycleID)
        ->select(DB::raw('cycle_budgets.id as id'),
        DB::raw('cycle_budgets.chart_id'),
        DB::raw('charts.is_accountable'),
        DB::raw('charts.code'),
        DB::raw('charts.name'),
        DB::raw('debit'),
        DB::raw('credit'))->get();


        return response()->json($cyclebudget);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\CycleBudget  $cycleBudget
    * @return \Illuminate\Http\Response
    */
    public function show(CycleBudget $cycleBudget)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\CycleBudget  $cycleBudget
    * @return \Illuminate\Http\Response
    */
    public function edit(CycleBudget $cycleBudget)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\CycleBudget  $cycleBudget
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, CycleBudget $cycleBudget)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\CycleBudget  $cycleBudget
    * @return \Illuminate\Http\Response
    */
    public function destroy(CycleBudget $cycleBudget)
    {
        //
    }
}
