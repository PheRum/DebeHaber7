<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Document;
use Illuminate\Http\Request;
use App\Http\Resources\GeneralResource;

class DocumentController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return GeneralResource::collection(
            Document::where('taxpayer_id', $taxPayer->id)
            ->orderBy('date', 'desc')
            ->paginate(50)
        );
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request,Taxpayer $taxPayer)
    {
        $document = $request->id == 0 ? $document = new Document() : Document::where('id', $request->id)->first();

        $document->prefix = $request->prefix;
        $document->type = $request->type;
        $document->mask = $request->mask;
        $document->start_range = $request->start_range;
        $document->current_range = $request->current_range;
        $document->end_range = $request->end_range;
        $document->code = $request->code;
        $document->taxpayer_id = $taxPayer->id;
        $document->code_expiry = $request->code_expiry;
        $document->save();

        return response()->json('ok', 200);
    }

    public function show(Taxpayer $taxPayer, Cycle $cycle, $transactionId)
    {
        return new GeneralResource(
            Document::where('taxpayer_id', $taxPayer->id)
            ->where('id', $transactionId)
            ->orderBy('date', 'desc')
            ->first()
        );
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Document  $document
    * @return \Illuminate\Http\Response
    */
    public function edit(Document $document)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Document  $document
    * @return \Illuminate\Http\Response
    */
    public function destroy(Document $document)
    {
        //
    }
}
