<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\AccountMovement;
use App\Http\Resources\GeneralResource;
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
        return GeneralResource::collection(
            AccountMovement::orderBy('date', 'des')
                ->with('chart:name,code')
                ->with('transaction:id,number,comment')
                ->with('currency:code')
                ->paginate(50)
        );
    }

    public function get_money_transferByID(Taxpayer $taxPayer, Cycle $cycle, $transferID)
    {
        $movement = AccountMovement::where('id', $transferID)->get();
        return response()->json($movement);
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
