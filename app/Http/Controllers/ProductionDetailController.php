<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\ProductionDetail;
use Illuminate\Http\Request;

class ProductionDetailController extends Controller
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
    * @param  \App\ProductionDetail  $productionDetail
    * @return \Illuminate\Http\Response
    */
    public function show(ProductionDetail $productionDetail)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\ProductionDetail  $productionDetail
    * @return \Illuminate\Http\Response
    */
    public function edit(ProductionDetail $productionDetail)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\ProductionDetail  $productionDetail
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, ProductionDetail $productionDetail)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\ProductionDetail  $productionDetail
    * @return \Illuminate\Http\Response
    */
    public function destroy(ProductionDetail $productionDetail)
    {
        //
    }
}
