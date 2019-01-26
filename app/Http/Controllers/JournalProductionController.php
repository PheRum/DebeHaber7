<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\JournalProduction;
use Illuminate\Http\Request;

class JournalProductionController extends Controller
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
    * @param  \App\JournalProduction  $journalProduction
    * @return \Illuminate\Http\Response
    */
    public function show(JournalProduction $journalProduction)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\JournalProduction  $journalProduction
    * @return \Illuminate\Http\Response
    */
    public function edit(JournalProduction $journalProduction)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\JournalProduction  $journalProduction
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, JournalProduction $journalProduction)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\JournalProduction  $journalProduction
    * @return \Illuminate\Http\Response
    */
    public function destroy(JournalProduction $journalProduction)
    {
        //
    }
}
