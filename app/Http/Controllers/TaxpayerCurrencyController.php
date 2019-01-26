<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\TaxpayerCurrency;
use Illuminate\Http\Request;

class TaxpayerCurrencyController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\TaxpayerCurrency  $taxpayerCurrency
    * @return \Illuminate\Http\Response
    */
    public function show(TaxpayerCurrency $taxpayerCurrency)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\TaxpayerCurrency  $taxpayerCurrency
    * @return \Illuminate\Http\Response
    */
    public function edit(TaxpayerCurrency $taxpayerCurrency)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\TaxpayerCurrency  $taxpayerCurrency
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, TaxpayerCurrency $taxpayerCurrency)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\TaxpayerCurrency  $taxpayerCurrency
    * @return \Illuminate\Http\Response
    */
    public function destroy(TaxpayerCurrency $taxpayerCurrency)
    {
        //
    }
}
