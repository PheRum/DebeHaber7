<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\AccountMovement;
use Illuminate\Http\Request;

class MoneyTransferController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return view('/commercial/money-transfers');
    }

    public function get_money_transfers(Taxpayer $taxPayer, Cycle $cycle, $skip)
    {
        $movements = AccountMovement::leftJoin('transactions', 'transactions.id', 'account_movements.transaction_id')
        ->join('currencies', 'currencies.id', 'account_movements.currency_id')
        ->join('charts', 'charts.id', 'account_movements.chart_id')
        ->where('account_movements.taxpayer_id', $taxPayer->id)
        ->select('account_movements.id as ID',
        'account_movements.date as Date',
        'charts.name as Account',
        'transactions.number as Number',
        'account_movements.credit as Credit',
        'account_movements.debit as Debit',
        'currencies.code as Currency',
        'account_movements.comment as Comment')
        ->skip($skip)
        ->take(100)
        ->get();

        return response()->json($movements);
    }

    public function get_money_transferByID(Taxpayer $taxPayer, Cycle $cycle, $transferID)
    {
        $movement = AccountMovement::where('id',$transferID)->get();
        return response()->json($movement);
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
        //
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\AccountMovement  $accountMovement
    * @return \Illuminate\Http\Response
    */
    public function show(AccountMovement $accountMovement)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\AccountMovement  $accountMovement
    * @return \Illuminate\Http\Response
    */
    public function edit(AccountMovement $accountMovement)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\AccountMovement  $accountMovement
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, AccountMovement $accountMovement)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\AccountMovement  $accountMovement
    * @return \Illuminate\Http\Response
    */
    public function destroy(AccountMovement $accountMovement)
    {
        //
    }
}
