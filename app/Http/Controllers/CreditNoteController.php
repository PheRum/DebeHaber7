<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Transaction;
use App\AccountMovement;
use App\TransactionDetail;
use App\JournalTransaction;
use App\Chart;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use DB;

class CreditNoteController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return TransactionResource::collection(
            Transaction::MyCreditNotes()
            ->with('customer:name,id')
            ->with('currency')
            ->with('details')
            ->whereBetween('date', [$cycle->start_date, $cycle->end_date])
            ->orderBy('transactions.date', 'desc')
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
        $Transaction = $request->id == 0 ? new Transaction() : Transaction::where('id', $request->id)->first();

        $Transaction->customer_id = $request->customer_id;
        $Transaction->supplier_id = $taxPayer->id;

        if ($request->document_id > 0)
        {
            $Transaction->document_id = $request->document_id;
        }

        $Transaction->currency_id = $request->currency_id;
        $Transaction->rate = $request->rate ?? 1;
        $Transaction->payment_condition = $request->payment_condition;

        if ($request->chart_account_id > 0)
        {
            $Transaction->chart_account_id = $request->chart_account_id;
        }

        $Transaction->date = $request->date;
        $Transaction->number = $request->number;
        $Transaction->code = $request->code;
        $Transaction->code_expiry = $request->code_expiry;
        $Transaction->comment = $request->comment;

        $Transaction->type = $request->type ?? 5;

        $Transaction->save();

        foreach ($request->details as $detail)
        {
            if ($detail['id'] == 0)
            {
                $TransactionDetail = new TransactionDetail();
            }
            else
            {
                $TransactionDetail = TransactionDetail::where('id',$detail['id'])->first();
            }

            $TransactionDetail->transaction_id = $Transaction->id;
            $TransactionDetail->chart_id = $detail['chart_id'];
            $TransactionDetail->chart_vat_id = $detail['chart_vat_id'];
            $TransactionDetail->value = $detail['value'];
            $TransactionDetail->save();
        }

        return response()->json('ok', 200);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function edit(Transaction $transaction)
    {
        $Transaction = Transaction::join('taxpayers', 'taxpayers.id', 'transactions.customer_id')
        ->where('supplier_id', $taxPayerID)
        ->where('transactions.id', $transaction->id)
        ->where('transactions.type', 5)
        ->with('details')
        ->select(DB::raw('false as selected,transactions.id,taxpayers.name as customer,
        customer_id,
        document_id,
        currency_id,
        rate,
        payment_condition,
        chart_account_id,
        date,
        number,
        type,
        transactions.code,
        code_expiry'))
        ->get();

        return response()->json($Transaction);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function destroy(Transaction $transaction)
    {
        try
        {
            AccountMovement::where('transaction_id', $transaction->id)->delete();
            //  JournalTransaction::where('transaction_id', $transaction->id)->delete();
            $transaction->delete();

            return response()->json('ok', 200);
        }
        catch (\Exception $e)
        {
            return response()->json($e, 500);
        }
    }

    public function generate_Journals($startDate, $endDate, $taxPayer, $cycle)
    {
        \DB::connection()->disableQueryLog();

        $queryCreditNotes = Transaction::MyCreditNotesForJournals($startDate, $endDate, $taxPayer->id)
        ->get();

        if ($queryCreditNotes->where('journal_id', '!=', null)->count() > 0)
        {
            $arrJournalIDs = $queryCreditNotes->where('journal_id', '!=', null)->pluck('journal_id');
            //## Important! Null all references of Journal in Transactions.
            Transaction::whereIn('journal_id', [$arrJournalIDs])
            ->update(['journal_id' => null]);

            //Delete the journals & details with id
            \App\JournalDetail::whereIn('journal_id', [$arrJournalIDs])
            ->forceDelete();
            \App\Journal::whereIn('id', [$arrJournalIDs])
            ->forceDelete();
        }

        $journal = new \App\Journal();
        $comment = __('accounting.CreditNoteComment', ['startDate' => $startDate->toDateString(), 'endDate' => $endDate->toDateString()]);

        $journal->cycle_id = $cycle->id; //TODO: Change this for specific cycle that is in range with transactions
        $journal->date = $endDate;
        $journal->comment = $comment;
        $journal->is_automatic = 1;
        $journal->save();

        //Assign all transactions the new journal_id.
        //No need for If Count > 0, because if it was 0, it would not have gone in this function.
        Transaction::whereIn('id', $queryCreditNotes->pluck('id'))
        ->update(['journal_id' => $journal->id]);

        $ChartController= new ChartController();

        //1st Query: Sales Transactions done in Credit. Must affect customer credit account.
        $listOfCreditNotes = Transaction::MyCreditNotesForJournals($startDate, $endDate, $taxPayer->id)
        ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
        ->groupBy('rate', 'customer_id')
        ->select(DB::raw('max(rate) as rate'),
        DB::raw('max(customer_id) as customer_id'),
        DB::raw('sum(transaction_details.value) as total'))
        ->get();

        //run code for credit purchase (insert detail into journal)
        foreach($listOfCreditNotes as $row)
        {
            $customerChartID = $ChartController->createIfNotExists_AccountsReceivables($taxPayer, $cycle, $row->customer_id)->id;
            $value = $row->total * $row->rate;
            $detail = $journal->details()->firstOrNew(['chart_id' => $customerChartID]);
            //$detail = $journal->details->where('chart_id', $customerChartID)->first() ?? new \App\JournalDetail();
            $detail->credit = 0;
            $detail->debit += $value;
            $detail->chart_id = $customerChartID;
            $journal->details()->save($detail);
            //  $journal->load('details');
        }

        //one detail query, to avoid being heavy for db. Group by fx rate, vat, and item type.
        $detailAccounts = Transaction::MyCreditNotesForJournals($startDate, $endDate, $taxPayer->id)
        ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
        ->join('charts', 'charts.id', '=', 'transaction_details.chart_vat_id')
        ->groupBy('rate', 'transaction_details.chart_id', 'transaction_details.chart_vat_id')
        ->select(DB::raw('max(rate) as rate'),
        DB::raw('max(charts.coefficient) as coefficient'),
        DB::raw('max(transaction_details.chart_vat_id) as chart_vat_id'),
        DB::raw('max(transaction_details.chart_id) as chart_id'),
        DB::raw('sum(transaction_details.value) as total'))
        ->get();

        //run code for credit purchase (insert detail into journal)
        foreach($detailAccounts->where('coefficient', '>', 0)->groupBy('chart_vat_id') as $groupedRow)
        {
            $groupTotal = $groupedRow->sum('total');
            $value = ($groupTotal - ($groupTotal / (1 + $groupedRow->first()->coefficient))) * $groupedRow->first()->rate;
            $detail = $journal->details()->firstOrNew(['chart_id' => $groupedRow->first()->chart_vat_id]);
            //$detail = $journal->details->where('chart_id', $groupedRow->first()->chart_vat_id)->first() ?? new \App\JournalDetail();
            $detail->credit += $value;
            $detail->debit = 0;
            $detail->chart_id = $groupedRow->first()->chart_vat_id;
            $journal->details()->save($detail);
            //$journal->load('details');
        }

        //run code for credit purchase (insert detail into journal)
        foreach($detailAccounts->groupBy('chart_id') as $groupedRow)
        {
            $value = 0;

            //Discount Vat Value for these items.
            foreach($groupedRow->groupBy('coefficient') as $row)
            {
                $value += ($row->sum('total') / (1 + $row->first()->coefficient)) * $row->first()->rate;
            }
            $detail = $journal->details()->firstOrNew(['chart_id' => $groupedRow->first()->chart_id]);
            //$detail = $journal->details->where('chart_id', $groupedRow->first()->chart_id)->first() ?? new \App\JournalDetail();
            $detail->credit += $value;
            $detail->debit = 0;
            $detail->chart_id = $groupedRow->first()->chart_id;
            $journal->details()->save($detail);
            //$journal->load('details');
        }
    }
}
