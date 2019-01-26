<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\JournalSim;
use Illuminate\Http\Request;

class JournalSimController extends Controller
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
    * @param  \App\JournalSim  $journalSim
    * @return \Illuminate\Http\Response
    */
    public function show(JournalSim $journalSim)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\JournalSim  $journalSim
    * @return \Illuminate\Http\Response
    */
    public function edit(JournalSim $journalSim)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\JournalSim  $journalSim
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, JournalSim $journalSim)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\JournalSim  $journalSim
    * @return \Illuminate\Http\Response
    */
    public function destroy(JournalSim $journalSim)
    {
        //
    }
}
