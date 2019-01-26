<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
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

    public function get_currency(Taxpayer $taxPayer)
    {
        $currency = Currency::select('id', 'name', 'code')
        ->where('country', $taxPayer->country)
        ->get();

        return response()->json($currency);
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
    * @param  \App\Currency  $currency
    * @return \Illuminate\Http\Response
    */
    public function edit(Currency $currency)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Currency  $currency
    * @return \Illuminate\Http\Response
    */
    public function destroy(Currency $currency)
    {
        //
    }
}
