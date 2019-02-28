<?php

namespace App\Http\Controllers;

use App\AccountMovement;
use App\Taxpayer;
use App\Cycle;
use App\Chart;
use App\Transaction;
use App\TransactionDetail;
use App\Http\Resources\GeneralResource;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class PurchaseController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        //TODO improve query using sum of deatils instead of inner join.
        return GeneralResource::collection(
            Transaction::MyPurchases()
            ->with('supplier:name,id')
            ->with('currency')
            ->with('details')
            ->whereBetween('date', [$cycle->start_date, $cycle->end_date])
            ->orderBy('date', 'desc')
            ->paginate(50)
        );
    }

    public function getLastSale($partnerId)
    {
        $transaction = Transaction::MyPurchases()
        ->where('customer_id', $partnerId)
        ->with('customer:name,id')
        ->with('details')
        ->last();

        return response()->json($transaction, 200);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request,Taxpayer $taxPayer,Cycle $cycle)
    {
        $transaction = Transaction::firstOrNew(['id' => $request->id]);
        $transaction->customer_id = $taxPayer->id;

        if ($request->supplier['id'] > 0)
        {
            $transaction->supplier_id = $request->supplier['id'];
        }

        $transaction->document_id = $request->document_id > 0 ? $request->document_id : null;
        $transaction->currency_id = $request->currency_id;
        $transaction->rate = $request->rate;
        $transaction->payment_condition = $request->payment_condition;

        if ($request->chart_account_id > 0)
        {
            $transaction->chart_account_id = $request->chart_account_id;
        }

        $transaction->date = $request->date;
        $transaction->number = $request->number;
        $transaction->code = $request->code;
        $transaction->code_expiry = $request->code_expiry;
        $transaction->comment = $request->comment;
        $transaction->type = $request->type ?? 1;
        $transaction->save();

        foreach ($request->details as $detail)
        {
            $transactionDetail = TransactionDetail::firstOrNew(['id' => $detail['id']]);
            $transactionDetail->transaction_id = $transaction->id;
            $transactionDetail->chart_id = $detail['chart_id'];
            $transactionDetail->chart_vat_id = $detail['chart_vat_id']>0?$detail['chart_vat_id']:null;
            $transactionDetail->value = $detail['value'];
            $transactionDetail->save();
        }

        return response()->json('Ok', 200);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function show(Taxpayer $taxPayer, Cycle $cycle, $transactionId)
    {
        return new GeneralResource(
            Transaction::MyPurchases()->with('supplier:name,taxid,id')
            ->where('id', $transactionId)
            ->with('details')
            ->first()
        );
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function destroy(Taxpayer $taxPayer, Cycle $cycle,$transactionID)
    {
        try
        {
            //TODO: Run Tests to make sure it deletes all journals related to transaction
            AccountMovement::where('transaction_id', $transactionID)->delete();
            //JournalTransaction::where('transaction_id',$transactionID)->delete();
            Transaction::where('id',$transactionID)->delete();

            return response()->json('ok', 200);
        }
        catch (\Exception $e)
        {
            return response()->json($e, 500);
        }
    }

    /**
    * Remove the specified resource from storage (Force Delete).
    *
    * @param  \App\Transaction  $transaction
    * @return \Illuminate\Http\Response
    */
    public function destroyForce(Taxpayer $taxPayer, Cycle $cycle, $transactionId)
    {
        try
        {
            //TODO: Run Tests to make sure it deletes all journals related to transaction
            AccountMovement::where('transaction_id', $transactionId)->forceDelete();
            //JournalTransaction::where('transaction_id',$transactionId)->delete();
            Transaction::where('id', $transactionId)->forceDelete();

            return response()->json('Ok', 200);
        }
        catch (\Exception $e)
        {
            return response()->json($e, 500);
        }
    }

    /*
    Purchase code to generate journals.
    */
    public function generate_Journals($startDate, $endDate, $taxPayer, $cycle)
    {
        \DB::connection()->disableQueryLog();

        $queryPurchases = Transaction::MyPurchasesForJournals($startDate, $endDate, $taxPayer->id)
        ->get();

        if ($queryPurchases->where('journal_id', '!=', null)->count() > 0)
        {
            $arrJournalIDs = $queryPurchases->where('journal_id', '!=', null)->pluck('journal_id');
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
        $comment = __('accounting.PurchaseBookComment', ['startDate' => $startDate->toDateString(), 'endDate' => $endDate->toDateString()]);

        $journal->cycle_id = $cycle->id; //TODO: Change this for specific cycle that is in range with transactions
        $journal->date = $endDate;
        $journal->comment = $comment;
        $journal->is_automatic = 1;
        $journal->save();

        //Assign all transactions the new journal_id.
        //No need for If Count > 0, because if it was 0, it would not have gone in this function.
        Transaction::whereIn('id', $queryPurchases->pluck('id'))
        ->update(['journal_id' => $journal->id]);

        $ChartController= new ChartController();

        //Sales Transactionsd done in cash. Must affect direct cash account.
        $cashPurchases = Transaction::MyPurchasesForJournals($startDate, $endDate, $taxPayer->id)
        ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
        ->groupBy('rate', 'chart_account_id')
        ->where('payment_condition', '=', 0)
        ->select(DB::raw('max(rate) as rate'),
        DB::raw('max(chart_account_id) as chart_account_id'),
        DB::raw('sum(transaction_details.value) as total'))
        ->get();

        //run code for cash purchase (insert detail into journal)
        foreach($cashPurchases as $row)
        {
            // search if chart exists, or else create it. we don't want an error causing all transactions not to be accounted.
            $accountChartID = $row->chart_account_id ?? $ChartController->createIfNotExists_CashAccounts($taxPayer, $cycle, $row->chart_account_id)->id;
            $value = $row->total * $row->rate;

            $detail = $journal->details->where('chart_id', $accountChartID)->first() ?? new \App\JournalDetail();
            $detail->credit = 0;
            $detail->debit += $value;
            $detail->chart_id = $accountChartID;
            $journal->details()->save($detail);
            $journal->load('details');
        }

        //2nd Query: Sales Transactions done in Credit. Must affect customer credit account.
        $creditPurchases = Transaction::MyPurchasesForJournals($startDate, $endDate, $taxPayer->id)
        ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
        ->groupBy('rate', 'supplier_id')
        ->where('payment_condition', '>', 0)
        ->select(DB::raw('max(rate) as rate'),
        DB::raw('max(supplier_id) as supplier_id'),
        DB::raw('sum(transaction_details.value) as total'))
        ->get();

        //run code for credit purchase (insert detail into journal)
        foreach($creditPurchases as $row)
        {
            $supplierChartID = $ChartController->createIfNotExists_AccountsPayable($taxPayer, $cycle, $row->supplier_id)->id;
            $value = $row->total * $row->rate;

            $detail = $journal->details()->firstOrNew(['chart_id' => $supplierChartID]);
            $detail->debit += $value;
            $detail->chart_id = $supplierChartID;
            $journal->details()->save($detail);
        }

        //one detail query, to avoid being heavy for db. Group by fx rate, vat, and item type.
        $detailAccounts = Transaction::MyPurchasesForJournals($startDate, $endDate, $taxPayer->id)
        ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
        ->join('charts', 'charts.id', '=', 'transaction_details.chart_vat_id')
        ->groupBy('rate', 'transaction_details.chart_id', 'transaction_details.chart_vat_id')
        ->select(DB::raw('max(rate) as rate'),
        DB::raw('max(charts.coefficient) as coefficient'),
        DB::raw('max(transaction_details.chart_vat_id) as chart_vat_id'),
        DB::raw('max(transaction_details.chart_id) as chart_id'),
        DB::raw('sum(transaction_details.value) as total'))
        ->get();

        //run code for credit sales (insert detail into journal)
        foreach($detailAccounts->where('coefficient', '>', 0) as $row)
        {
            $detail = $journal->details()->firstOrNew(['chart_id' => $row->chart_vat_id]);
            $detail->credit += ($row->total - ($row->total / (1 + $row->coefficient))) * $row->rate;
            $journal->details()->save($detail);
        }

        //run code for credit sales (insert detail into journal)
        foreach($detailAccounts as $row)
        {
            $detail = $journal->details()->firstOrNew(['chart_id' => $row->chart_id]);
            $detail->credit += ($row->total / (1 + $row->coefficient)) * $row->rate;;
            $journal->details()->save($detail);
        }
    }
}
