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

class AccountReceivableController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return ModelResource::collection(Transaction::MySales()
        ->join('taxpayers', 'taxpayers.id', 'transactions.customer_id')
        ->join('currencies', 'transactions.currency_id','currencies.id')
        ->join('transaction_details as td', 'td.transaction_id', 'transactions.id')
        ->where('transactions.supplier_id', $taxPayer->id)
        ->where('transactions.payment_condition', '>', 0)
        ->groupBy('transactions.id')
        ->select(DB::raw('max(transactions.id) as id'),
        DB::raw('max(taxpayers.name) as Customer'),
        DB::raw('max(taxpayers.taxid) as CutomerTaxID'),
        DB::raw('max(currencies.code) as Currency'),
        DB::raw('max(transactions.payment_condition) as PaymentCondition'),
        DB::raw('max(transactions.date) as Date'),
        DB::raw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY) as Expiry'),
        DB::raw('max(transactions.number) as Number'),
        DB::raw('(select ifnull(sum(account_movements.credit * account_movements.rate), 0)  from account_movements where `transactions`.`id` = `account_movements`.`transaction_id`) as Paid'),
        DB::raw('sum(td.value * transactions.rate) as Value'),
        DB::raw('(sum(td.value * transactions.rate)
        - (select
        ifnull(sum(account_movements.credit * account_movements.rate), 0)
        from account_movements
        where transactions.id = account_movements.transaction_id))
        as Balance')
        )
        ->orderByRaw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY)', 'desc')
        ->orderByRaw('max(transactions.number)', 'desc')
        ->paginate(100));
    }

    public function get_account_receivableByID(Taxpayer $taxPayer, Cycle $cycle, $id)
    {
        $accountMovement = $transactions = Transaction::MySales()
        ->join('taxpayers', 'taxpayers.id', 'transactions.customer_id')
        ->join('currencies', 'transactions.currency_id','currencies.id')
        ->join('transaction_details as td', 'td.transaction_id', 'transactions.id')
        ->where('transactions.supplier_id', $taxPayer->id)
        ->where('transactions.payment_condition', '>', 0)
        ->where('transactions.id', $id)
        ->groupBy('transactions.id')
        ->select(DB::raw('max(transactions.id) as id'),
        DB::raw('max(taxpayers.name) as Customer'),
        DB::raw('max(taxpayers.taxid) as CutomerTaxID'),
        DB::raw('max(currencies.code) as currency_code'),
        DB::raw('max(transactions.payment_condition) as payment_condition'),
        DB::raw('max(transactions.date) as date'),
        DB::raw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY) as code_expiry'),
        DB::raw('max(transactions.number) as number'),
        DB::raw('(select ifnull(sum(account_movements.credit * account_movements.rate), 0)  from account_movements where `transactions`.`id` = `account_movements`.`transaction_id`) as Paid'),
        DB::raw('sum(td.value * transactions.rate) as Value'),
        DB::raw('(sum(td.value * transactions.rate)
        - (select
        ifnull(sum(account_movements.credit * account_movements.rate), 0)
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
            $accountMovement->chart_id =$request->chart_account_id ;
            $accountMovement->date = $request->date;

            $accountMovement->transaction_id = $request->id != '' ? $request->id : null;
            $accountMovement->currency_id = $request->currency_id;
            $accountMovement->rate = $request->rate;
            $accountMovement->credit = $request->payment_value != '' ? $request->payment_value : 0;
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
    public function destroy(Taxpayer $taxPayer, Cycle $cycle,$transactionID)
    {
        // try
        // {
        //     //TODO: Run Tests to make sure it deletes all journals related to transaction
        //     AccountMovement::where('transaction_id', $transactionID)->delete();
        //     JournalTransaction::where('transaction_id',$transactionID)->delete();
        //     Transaction::where('id',$transactionID)->delete();
        //
        //     return response()->json('ok', 200);
        // }
        // catch (\Exception $e)
        // {
        //     return response()->json($e, 500);
        // }
    }

    public function generate_Journals($startDate, $endDate)
    {
        //Create chart controller we might need it further in the code to lookup charts.
        $ChartController = new ChartController();

        //get sum of all transactions divided by exchange rate.
        $journal = new Journal();
        $journal->cycle_id = $this->cycle->id; //TODO: Change this for specific cycle that is in range with transactions
        $journal->date = $accMovements->last()->date; //
        $journal->comment = $comment;
        $journal->save();

        //Affect all Cash Sales and uses Cash Accounts
        foreach ($accMovements->groupBy('chart_id') as $groupedByAccount)
        {
            $value = 0;

            //calculate value by currency. fx. TODO, Include Rounding depending on Main Curreny from Taxpayer Country.
            foreach ($groupedByAccount->groupBy('rate') as $groupedByRate)
            {
                $value += $groupedByRate->sum('credit') * $groupedByRate->first()->rate;
            }

            if ($value > 0)
            {
                //Check for Cash Account used.
                $chart = $ChartController->createIfNotExists_CashAccounts($this->taxPayer, $this->cycle, $groupedByAccount->first()->chart_id);

                $detail = new JournalDetail();
                $detail->debit = 0;
                $detail->credit = $value;
                $detail->chart_id = $chart->id;
                $detail->journal_id = $journal->id;
                $detail->save();
            }
        }

        //Affect all Cash Sales and uses Cash Accounts
        foreach ($accMovements->transaction->groupBy('customer_id') as $groupedByInvoice)
        {
            $value = 0;

            //calculate value by currency. fx. TODO, Include Rounding depending on Main Curreny from Taxpayer Country.
            foreach ($groupedByInvoice->groupBy('rate') as $groupedByRate)
            {
                $value += $groupedByRate->sum('credit') * $groupedByRate->first()->rate;
            }

            if ($value > 0)
            {
                //Check for Account Receivables used.
                $chart = $ChartController->createIfNotExists_AccountsReceivables($this->taxPayer, $this->cycle, $groupedByInvoice->first()->customer_id);

                $detail = new JournalDetail();
                $detail->debit = $value;
                $detail->credit = 0;
                $detail->chart_id = $chart->id;
                $detail->journal_id = $journal->id;
                $detail->save();
            }
        }

        foreach ($accMovements as $mov)
        {
            $mov->setStatus('Accounted');

            $journalAccountMovement = new JournalAccountMovement();
            $journalAccountMovement->journal_id = $journal->id;
            $journalAccountMovement->account_movement_id = $mov->id;
            $journalAccountMovement->save();
        }
    }
}
