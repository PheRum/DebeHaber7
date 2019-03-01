<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Taxpayer;
use App\Transaction;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function my(Taxpayer $taxPayer, $query)
    {
        Transaction::select('partner_name', 'partner_taxid')
        ->groupBy('partner_name', 'partner_taxid')
        ->where('taxpayer_id', $taxPayer->id)
        ->where(function($q) use($query) {
            $q->where('partner_name', 'like', '%' . $query . '%')
            ->orWhere('partner_taxid', 'like', '%' . $query . '%');
        })
        ->get();
    }

    public function groupByCountry(Taxpayer $taxPayer, $query)
    {
        Transaction::select('partner_name', 'partner_taxid')
        ->groupBy('partner_name', 'partner_taxid')
        ->where('taxpayer_id', $taxPayer->id)
        ->where(function($q) use($query) {
            $q->where('partner_name', 'like', '%' . $query . '%')
            ->orWhere('partner_taxid', 'like', '%' . $query . '%');
        })
        ->get();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
