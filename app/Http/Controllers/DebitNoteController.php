<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Transaction;
use App\TransactionDetail;
use App\AccountMovement;
use App\Chart;
use App\Http\Resources\GeneralResource;
use Illuminate\Http\Request;
use DB;

class DebitNoteController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return GeneralResource::collection(
            Transaction::MyDebitNotes()
                ->with('details')
                ->whereBetween('date', [$cycle->start_date, $cycle->end_date])
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
    public function store(Request $request, Taxpayer $taxPayer, Cycle $cycle)
    {
        (new TransactionController())->store($request, $taxPayer);
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
            Transaction::MyDebitNotes()->with('supplier:name,taxid,id')
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
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json('ok', 200);
    }

    public function generate_Journals($startDate, $endDate, $taxPayer, $cycle)
    {
        \DB::connection()->disableQueryLog();

        $queryPurchases = Transaction::MyDebitNotesForJournals($startDate, $endDate, $taxPayer->id)
            ->get();

        if ($queryPurchases->where('journal_id', '!=', null)->count() > 0) {
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
        $comment = __('accounting.DebitNoteComment', ['startDate' => $startDate->toDateString(), 'endDate' => $endDate->toDateString()]);

        $journal->cycle_id = $cycle->id; //TODO: Change this for specific cycle that is in range with transactions
        $journal->date = $endDate;
        $journal->comment = $comment;
        $journal->is_automatic = 1;
        $journal->save();

        //Assign all transactions the new journal_id.
        //No need for If Count > 0, because if it was 0, it would not have gone in this function.
        Transaction::whereIn('id', $queryPurchases->pluck('id'))
            ->update(['journal_id' => $journal->id]);

        $ChartController = new ChartController();

        //1st Query: Sales Transactions done in Credit. Must affect customer credit account.
        $listOfDebitNotes = Transaction::MyDebitNotesForJournals($startDate, $endDate, $taxPayer->id)
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->groupBy('rate', 'supplier_id')
            //->where('payment_condition', '>', 0) TODO, do not apply payment condition to debit note.
            ->select(
                DB::raw('max(rate) as rate'),
                DB::raw('max(supplier_id) as supplier_id'),
                DB::raw('sum(transaction_details.value) as total')
            )
            ->get();

        //run code for credit purchase (insert detail into journal)
        foreach ($listOfDebitNotes as $row) {
            $supplierChartID = $ChartController->createIfNotExists_AccountsPayable($taxPayer, $cycle, $row->supplier_id)->id;

            $value = $row->total * $row->rate;
            $detail = $journal->details()->firstOrNew(['chart_id' => $supplierChartID]);
            //$detail = $journal->details->where('chart_id', $supplierChartID)->first() ?? new \App\JournalDetail();
            $detail->debit = 0;
            $detail->credit += $value;
            $detail->chart_id = $supplierChartID;
            $journal->details()->save($detail);
            //$journal->load('details');
        }

        //one detail query, to avoid being heavy for db. Group by fx rate, vat, and item type.
        $detailAccounts = Transaction::MyDebitNotesForJournals($startDate, $endDate, $taxPayer->id)
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('charts', 'charts.id', '=', 'transaction_details.chart_vat_id')
            ->groupBy('rate', 'transaction_details.chart_id', 'transaction_details.chart_vat_id')
            ->select(
                DB::raw('max(rate) as rate'),
                DB::raw('max(charts.coefficient) as coefficient'),
                DB::raw('max(transaction_details.chart_vat_id) as chart_vat_id'),
                DB::raw('max(transaction_details.chart_id) as chart_id'),
                DB::raw('sum(transaction_details.value) as total')
            )
            ->get();

        //run code for credit purchase (insert detail into journal)
        foreach ($detailAccounts->where('coefficient', '>', 0)->groupBy('chart_vat_id') as $groupedRow) {
            $groupTotal = $groupedRow->sum('total');
            $value = ($groupTotal - ($groupTotal / (1 + $groupedRow->first()->coefficient))) * $groupedRow->first()->rate;
            $detail = $journal->details()->firstOrNew(['chart_id' => $groupedRow->first()->chart_vat_id]);
            //  $detail = $journal->details->where('chart_id', $groupedRow->first()->chart_vat_id)->first() ?? new \App\JournalDetail();
            $detail->debit += $value;
            $detail->credit = 0;
            $detail->chart_id = $groupedRow->first()->chart_vat_id;
            $journal->details()->save($detail);
            //$journal->load('details');
        }

        //run code for credit purchase (insert detail into journal)
        foreach ($detailAccounts->groupBy('chart_id') as $groupedRow) {
            $value = 0;

            //Discount Vat Value for these items.
            foreach ($groupedRow->groupBy('coefficient') as $row) {
                $value += ($row->sum('total') / (1 + $row->first()->coefficient)) * $row->first()->rate;
            }
            $detail = $journal->details()->firstOrNew(['chart_id' => $groupedRow->first()->chart_id]);
            //$detail = $journal->details->where('chart_id', $groupedRow->first()->chart_id)->first() ?? new \App\JournalDetail();
            $detail->debit += $value;
            $detail->credit = 0;
            $detail->chart_id = $groupedRow->first()->chart_id;
            $journal->details()->save($detail);
            //$journal->load('details');
        }
    }
}
