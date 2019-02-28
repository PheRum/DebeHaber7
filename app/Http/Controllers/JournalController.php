<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\AccountMovement;
use App\Transaction;
use App\Taxpayer;
use App\Cycle;
use App\Journal;
use App\JournalDetail;
use App\Http\Resources\GeneralResource;
use App\Jobs\GenerateJournal;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\JournalCollection;
use Illuminate\Support\Facades\Log;

use Laravel\Spark\Contracts\Repositories\NotificationRepository;

class JournalController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        
        return GeneralResource::collection(
            Journal::with(['details:journal_id,chart_id,debit,credit',
            'details.chart:id,name,code,type'])
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
    public function store(Request $request,Taxpayer $taxPayer,Cycle $cycle)
    {
        $journal = $request->id == 0 ? new Journal() : Journal::where('id', $request->id)->first();

        $journal->date = $request->date;
        $journal->number = $request->number ;
        $journal->comment = $request->comment;
        $journal->cycle_id = $cycle->id;

        $journal->save();

        foreach ($request->details as $detail)
        {
            $journalDetail = JournalDetail::firstOrNew(['id' => $detail['id']]);;
            $journalDetail->journal_id = $journal->id;
            $journalDetail->chart_id = $detail['chart_id'];
            $journalDetail->debit = $detail['debit'];
            $journalDetail->credit = $detail['credit'];
            $journalDetail->save();
        }

        return response()->json('Ok', 200);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Journal  $journal
    * @return \Illuminate\Http\Response
    */
    public function show(Taxpayer $taxPayer, Cycle $cycle, $journalId)
    {
        return new GeneralResource(
            Journal::with(['details',
            'details.chart:id,name,code,type'])
            ->where('id', $journalId)
            ->orderBy('date', 'desc')
            ->first()
        );
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Journal  $journal
    * @return \Illuminate\Http\Response
    */
    public function destroy(Journal $journal)
    {
        //clean up use of journal in transactions and other places
    }

    public function generateJournalsByRange(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate)
    {
        GenerateJournal::dispatch($taxPayer, $cycle, $startDate, $endDate);
        return back();
    }
}
