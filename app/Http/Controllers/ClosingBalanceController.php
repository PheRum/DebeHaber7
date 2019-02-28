<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Journal;
use App\JournalDetail;
use App\Chart;
use App\Http\Resources\GeneralResource;
use DB;
use Illuminate\Http\Request;

class ClosingBalanceController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        $journalDetails = JournalDetail::whereHas('journal', function ($query) use($cycle) {
            $query->where('cycle_id', $cycle->id)
            ->where('is_last', 1);
        })
        ->get();

        //get list of charts.
        $charts = Chart::My($taxPayer, $cycle)
        ->select('id',
        'code',
        'name',
        'type',
        'sub_type',
        'is_accountable',
        DB::raw('null as debit'),
        DB::raw('null as credit'),
        DB::raw('null as journal_id'))
        ->orderBy('code')
        ->get();

        if (isset($journalDetails))
        {
            // Loop through Journal entries and add to chart balance
            foreach ($journalDetails->groupBy('chart_id') as $journalGrouped)
            {
                $chart = $charts->where('id', $journalGrouped->first()->chart_id)->first();

                if (isset($chart))
                {
                    $chart->id = $journalGrouped->first()->id;
                    $chart->debit = $journalGrouped->sum('debit');
                    $chart->credit = $journalGrouped->sum('credit');
                }
            }
        }

        $closingBalance = $charts->sortBy('type')->sortBy('code');
        return response()->json(GeneralResource::collection($closingBalance));
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request,Taxpayer $taxPayer, Cycle $cycle)
    {

        $journal =  Journal::where('is_last', true)->where('cycle_id',$cycle->id)->first() ?? new Journal();

        $journal->date = $cycle->end_date;
        $journal->comment = $cycle->year . ' - ' . __('accounting.ClosingBalance');
        $journal->is_last = true;
        $journal->cycle_id = $cycle->id;
        $journal->save();

        $details = collect($request)->where('is_accountable',  1);
        foreach ($details as $detail)
        {

            // JournalDetail::where('id', $detail->journal_id)->first() ??
            $journalDetail = new JournalDetail();

            $journalDetail->journal_id = $journal->id;
            $journalDetail->chart_id = $detail['id'];
            $journalDetail->debit = $detail['debit'] ?? 0;
            $journalDetail->credit = $detail['credit'] ?? 0;

            //Save only if there are values ot be saved. avoid saving blank values.
            if ($journalDetail->debit > 0 || $journalDetail->credit > 0)
            {
                $journalDetail->save();
            }
        }

        return response()->json('Ok', 200);
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
