<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\JournalDetail;
use Illuminate\Http\Request;

class JournalDetailController extends Controller
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
    * @param  \App\JournalDetail  $journalDetail
    * @return \Illuminate\Http\Response
    */
    public function show(JournalDetail $journalDetail)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\JournalDetail  $journalDetail
    * @return \Illuminate\Http\Response
    */
    public function edit(JournalDetail $journalDetail)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\JournalDetail  $journalDetail
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, JournalDetail $journalDetail)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\JournalDetail  $journalDetail
    * @return \Illuminate\Http\Response
    */
    public function destroy(JournalDetail $journalDetail)
    {
        //
    }
}
