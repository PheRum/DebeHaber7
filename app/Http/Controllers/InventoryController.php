<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Chart;
use App\Inventory;
use App\Transaction;
use App\Journal;
use App\Http\Resources\GeneralResource;
use Illuminate\Http\Request;
use DB;

class InventoryController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        // $inventory = Inventory::skip($skip)
        // ->take(100)
        // ->get();
        // return response()->json($inventory);

        return GeneralResource::collection(
            Transaction::whereBetween('date', [$cycle->start_date, $cycle->end_date])
            ->orderBy('date', 'desc')
            ->paginate(50)
        );
    }

    // public function get_inventoryChartType(Taxpayer $taxPayer, Cycle $cycle)
    // {
    //     $transaction = Chart::where('chart_version_id', $cycle->chart_version_id)
    //     ->where('type', 4)
    //     ->where('sub_type', 4)
    //     ->get();
    //
    //     return response()->json($transaction);
    // }

    //TODO pass start and end date to calculate sales.
    public function calc_sales(Request $request, Taxpayer $taxPayer, Cycle $cycle)
    {
        $transaction = Transaction::MySales()
        ->leftJoin('transaction_details as td', 'td.transaction_id', 'transactions.id')
        ->whereIn('td.chart_id', $request->selectcharttype)
        ->whereBetween('date', [$request->start_date, $request->end_date])
        ->groupBy('td.chart_id')
        ->select(DB::raw('sum(td.value) as sales'),
        DB::raw('sum(td.cost) as cost_value'))
        ->get();

        return response()->json($transaction);
    }

    //TODO pass start date to calculate sales at beging of inventory range
    public function calc_invenotryValue(Request $request, Taxpayer $taxPayer, Cycle $cycle)
    {
        $journals = Journal::leftJoin('journal_details as jd', 'jd.journal_id', 'journals.id')
        ->where('journals.cycle_id', $cycle->id)
        ->where('jd.chart_id',$request->chart_id)
        ->whereBetween('date', [$request->start_date, $request->end_date])
        ->groupBy('jd.chart_id')
        ->select(DB::raw('sum(td.debit) as inventory_value'))
        ->get();
        return response()->json($journals ?? 0);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request, Taxpayer $taxPayer,Cycle $cycle)
    {
        if ($request->id == 0)
        {
            $inventory = new Inventory();
        }
        else
        {
            $inventory = Inventory::where('id', $request->id)->first();
        }

        $inventory->taxpayer_id = $taxPayer->id;
        $inventory->chart_id =$request->chart_id ;
        $inventory->start_date = $request->start_date;
        $inventory->end_date = $request->end_date;
        $inventory->sales_value = $request->sales_value;
        $inventory->cost_value = $request->cost_value;
        $inventory->inventory_value = $request->inventory_value;
        $inventory->chart_of_incomes =implode(' ', $request->selectcharttype) ;
        $inventory->comments = $request->comment;

        $inventory->save();

        return response()->json('ok', 200);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Inventory  $transaction
    * @return \Illuminate\Http\Response
    */
    public function show(Taxpayer $taxPayer, Cycle $cycle, $inventoryId)
    {
        return new GeneralResource(
            Inventory::where('id', $inventoryId)
            ->first()
        );
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Inventory  $inventory
    * @return \Illuminate\Http\Response
    */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
