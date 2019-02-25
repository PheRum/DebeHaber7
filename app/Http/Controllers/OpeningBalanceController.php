<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Journal;
use App\JournalDetail;
use App\Chart;
use App\Http\Resources\BalanceResource;
use Illuminate\Http\Request;
use DB;

class OpeningBalanceController extends Controller
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
            ->where('is_first', 1);
        })
        ->get();

        //get list of charts.
        $charts =  Chart::My($taxPayer, $cycle)
        ->select('id', 'code', 'name',
        'type', 'sub_type', 'is_accountable',
        DB::raw('null as debit'),
        DB::raw('null as credit'),
        DB::raw('null as detail_id'))
        ->orderBy('code')
        ->get();

        if (isset($journalDetails))
        {
            // Loop through Journal entries and add to chart balance
            foreach ($journalDetails as $detail)
            {
                $chart = $charts->where('id', $detail->chart_id)->first();

                if (isset($chart))
                {
                    $chart->detail_id = $detail->id_text;
                    $chart->debit = $detail->debit;
                    $chart->credit = $detail->credit;
                }
            }
        }

        $openingBalance = $charts->sortBy('type')->sortBy('code');
        return response()->json(BalanceResource::collection($openingBalance));
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request,Taxpayer $taxPayer, Cycle $cycle)
    {
        // $journal = Journal::where('is_first', true)->where('cycle_id', $cycle->id)->first() ?? new Journal();
        $journal = Journal::firstOrNew(['cycle_id' => $cycle->id, 'is_first' => true]);

        $journal->date = $cycle->start_date;
        $journal->comment = $cycle->year . ' - ' . __('accounting.OpeningBalance');
        $journal->is_first= true;
        $journal->cycle_id = $cycle->id;
        $journal->save();

        $details = collect($request)->where('is_accountable', '=', 1);

        foreach ($details as $detail)
        {
            if ($detail['id'] > 0)
            {
                $journalDetail = JournalDetail::withUuid($detail['detail_id'])->first() ?? new JournalDetail();
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
        }

        return response()->json("Ok", 200);
    }
}
