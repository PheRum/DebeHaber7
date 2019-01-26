<?php

namespace App\Http\Controllers;

use App\AccountMovement;
use App\JournalTransaction;
use App\Transaction;
use App\Taxpayer;
use App\Cycle;
use App\Chart;
use App\Http\Resources\ModelResource;
use Illuminate\Http\Request;
use DB;

class AccountPayableController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        $chart = Chart::MoneyAccounts()->orderBy('name')
        ->select('name', 'id', 'sub_type')
        ->get();

        return view('/commercial/accounts-payable')->with('charts',$chart);
    }

    public function get_account_payable(Taxpayer $taxPayer, Cycle $cycle)
    {
        $transactions = Transaction::MyPurchases()
        ->join('taxpayers', 'taxpayers.id', 'transactions.supplier_id')
        ->join('currencies', 'transactions.currency_id','currencies.id')
        ->join('transaction_details as td', 'td.transaction_id', 'transactions.id')
        ->where('transactions.customer_id', $taxPayer->id)
        ->where('transactions.payment_condition', '>', 0)
        ->groupBy('transactions.id')
        ->select(DB::raw('max(transactions.id) as id'),
        DB::raw('max(taxpayers.name) as Supplier'),
        DB::raw('max(taxpayers.taxid) as SupplierTaxID'),
        DB::raw('max(currencies.code) as Currency'),
        DB::raw('max(transactions.payment_condition) as PaymentCondition'),
        DB::raw('max(transactions.date) as Date'),
        DB::raw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY) as Expiry'),
        DB::raw('max(transactions.number) as Number'),
        DB::raw('(select ifnull(sum(account_movements.debit * account_movements.rate), 0)  from account_movements where `transactions`.`id` = `account_movements`.`transaction_id`) as Paid'),
        DB::raw('sum(td.value * transactions.rate) as Value'),
        DB::raw('(sum(td.value * transactions.rate)
        - (select
        ifnull(sum(account_movements.debit * account_movements.rate), 0)
        from account_movements
        where transactions.id = account_movements.transaction_id))
        as Balance')
        )
        ->orderByRaw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY)', 'desc')
        ->orderByRaw('max(transactions.number)', 'desc')
        ->paginate(100);

        return ModelResource::collection($transactions);
    }

    public function get_account_payableByID(Taxpayer $taxPayer, Cycle $cycle,$id)
    {
        $accountMovement = Transaction::MyPurchases()
        ->join('taxpayers', 'taxpayers.id', 'transactions.supplier_id')
        ->join('currencies', 'transactions.currency_id','currencies.id')
        ->join('transaction_details as td', 'td.transaction_id', 'transactions.id')
        ->where('transactions.customer_id', $taxPayer->id)
        ->where('transactions.id', $id)
        ->where('transactions.payment_condition', '>', 0)
        ->groupBy('transactions.id')
        ->select(DB::raw('max(transactions.id) as id'),
        DB::raw('max(taxpayers.name) as Supplier'),
        DB::raw('max(taxpayers.taxid) as SupplierTaxID'),
        DB::raw('max(currencies.code) as currency_code'),
        DB::raw('max(transactions.payment_condition) as payment_condition'),
        DB::raw('max(transactions.date) as date'),
        DB::raw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY) as code_expiry'),
        DB::raw('max(transactions.number) as number'),
        DB::raw('(select ifnull(sum(account_movements.debit * account_movements.rate), 0)  from account_movements where `transactions`.`id` = `account_movements`.`transaction_id`) as Paid'),
        DB::raw('sum(td.value * transactions.rate) as Value'),
        DB::raw('(sum(td.value * transactions.rate) - (select
        ifnull(sum(account_movements.debit * account_movements.rate), 0)
        from account_movements
        where transactions.id = account_movements.transaction_id))
        as Balance')
        )
        ->get();

        return response()->json($accountMovement);
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
        if ($request->payment_value > 0)
        {
            $accountMovement = new AccountMovement();
            $accountMovement->taxpayer_id = $request->taxpayer_id;
            $accountMovement->chart_id = $request->chart_account_id ;
            $accountMovement->date = $request->date;

            $accountMovement->transaction_id = $request->id != '' ? $request->id : null;
            $accountMovement->currency_id = $request->currency_id;
            $accountMovement->rate = $request->rate ?? 1;
            $accountMovement->debit = $request->payment_value != '' ? $request->payment_value : 0;
            $accountMovement->comment = $request->comment;

            $accountMovement->save();

            return response()->json('ok', 200);
        }

        return response()->json('no value', 403);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\AccountMovement  $accountMovement
    * @return \Illuminate\Http\Response
    */
    public function show(AccountMovement $accountMovement)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\AccountMovement  $accountMovement
    * @return \Illuminate\Http\Response
    */
    public function edit(AccountMovement $accountMovement)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\AccountMovement  $accountMovement
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, AccountMovement $accountMovement)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\AccountMovement  $accountMovement
    * @return \Illuminate\Http\Response
    */
    public function destroy(Taxpayer $taxPayer, Cycle $cycle, $transactionID)
    {
        // try
        // {
        //     //TODO: Run Tests to make sure it deletes all journals related to transaction
        //     AccountMovement::where('transaction_id', $transactionID)->delete();
        //     JournalTransaction::where('transaction_id', $transactionID)->delete();
        //     Transaction::where('id', $transactionID)->delete();
        //
        //     return response()->json('ok', 200);
        // }
        // catch (\Exception $e)
        // {
        //     return response()->json($e, 500);
        // }
    }

    public function generate_Journals($startDate, $endDate, $taxPayer, $cycle)
    {
        \DB::connection()->disableQueryLog();

        $queryAccountMovements = AccountMovement::My($startDate, $endDate, $taxPayer->id);

        if ($queryAccountMovements->where('journal_id', '!=', null)->count() > 0)
        {
            $arrJournalIDs = $queryAccountMovements->where('journal_id', '!=', null)->pluck('journal_id')->get();

            //## Important! Null all references of Journal in Transactions.
            AccountMovement::whereIn('journal_id', [$arrJournalIDs])
            ->update(['journal_id' => null]);

            //Delete the journals & details with id
            \App\JournalDetail::whereIn('journal_id', [$arrJournalIDs])
            ->forceDelete();

            \App\Journal::whereIn('id', [$arrJournalIDs])
            ->forceDelete();
        }

        $journal = new \App\Journal();
        $comment = __('Payments Made', ['startDate' => $startDate->toDateString(), 'endDate' => $endDate->toDateString()]);

        $journal->cycle_id = $cycle->id;
        $journal->date = $endDate;
        $journal->comment = $comment;
        $journal->is_automatic = 1;
        $journal->save();

        $chartController = new ChartController();

        //2nd Query: Movements related to Credit Purchases. Cash Purchases are ignored.
        $listOfPayables = AccountMovement::My($startDate, $endDate, $taxPayer->id)
        ->whereHas('transaction', function($q) use($taxPayer) {
            $q->where('customer_id', '=', $taxPayer->id)
            ->where('payment_condition', '>', 0);
        })->get();

        //run code for credit purchase (insert detail into journal)
        foreach($listOfPayables as $row)
        {
            $value = $row->debit * $row->rate;

            if ($value == 0)
            {
                continue;
            }

            //First clean Accounts Receivables with localized currency value.
            $partnerChartID = $chartController->createIfNotExists_AccountsPayable($taxPayer, $cycle, $row->transaction->supplier_id)->id;
            $detail = $journal->details()->firstOrNew(['chart_id' => $partnerChartID]);
            $detail->credit += $value;
            $detail->chart_id = $partnerChartID;
            $journal->details()->save($detail);

            //Second clean Accounts with same localized currency value.
            $detail = $journal->details()->firstOrNew(['chart_id' => $row->chart_id]);
            $detail->debit += $value;
            $detail->chart_id = $row->chart_id;
            $journal->details()->save($detail);

            //Third, Verify Transaction Currency Rate vs Payment Currency Rate to calculate profit or loss by exchange rate differences
            $invoiceRate = $row->transaction->rate;
            $paymentRate = $row->rate;
            $rateDifference = abs($invoiceRate - $paymentRate);

            if ($paymentRate < $invoiceRate) //Gain by Exchange Rante Difference
            {
                $detail = new \App\JournalDetail();
                $detail->credit = $row->credit * $rateDifference;
                $detail->debit = 0;
                $detail->chart_id = $ChartController->createIfNotExists_IncomeFromFX($taxPayer, $cycle)->id;
                $journal->details()->save($detail);
            }
            else if($paymentRate > $invoiceRate) //Loss by Exchange Rante Difference
            {
                $detail = new \App\JournalDetail();
                $detail->credit = 0;
                $detail->debit = $row->credit * $rateDifference;
                $detail->chart_id = $ChartController->createIfNotExists_ExpenseFromFX($taxPayer, $cycle)->id;
                $journal->details()->save($detail);
            }
        }

        if ($journal->details()->count() == 0)
        {
            $journal->details()->delete();
            $journal->delete();
        }

        AccountMovement::whereIn('id', $queryAccountMovements->pluck('id'))
        ->update(['journal_id' => $journal->id]);
    }
}
