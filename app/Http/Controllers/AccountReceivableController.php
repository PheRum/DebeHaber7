<?php

namespace App\Http\Controllers;

use App\AccountMovement;
use App\Transaction;
use App\Taxpayer;
use App\Cycle;
use App\Chart;
use App\Http\Resources\GeneralResource;
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
    return GeneralResource::collection(
      //model balance column calculated
      //whereHas(function sum of accMove < totalDetail)
      Transaction::MySales()
    //  ->joins()
    //  ->select('*')
    //  ->where('')
      ->where('payment_condition', '>', 0)
      ->with('currency:code')
      ->with('details:value')
      ->with('customer:name,taxid,id')
      ->with('accountMovements:credit,debit,rate')
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
    if ($request->payment_value > 0 &&  $request->id != '') {
      $accountMovement = AccountMovement::where('transaction_id',$request->id)->first();
      $accountMovement->taxpayer_id = $request->taxpayer_id;
      $accountMovement->chart_id = $request->chart_account_id;
      $accountMovement->date = $request->date;

      $accountMovement->transaction_id = $request->id;
      $accountMovement->currency_id = $request->currency_id;
      $accountMovement->rate = $request->rate ?? 1;
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
  public function show(Taxpayer $taxPayer, Cycle $cycle,$transactionId)
  {

    return new GeneralResource(
      Transaction::MySales()
      // ->where('payment_condition', '>', 0)
      ->with('currency:code')
      ->with('details:value')
      ->with('customer:name,taxid,id')
      ->with('accountMovements:credit,debit,rate')
      ->where('id', $transactionId)
      ->first()
    );
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
    $journal->date = $endDate; //
    $journal->comment = __('accounting.AccountReceivableComment');
    $journal->save();

    $accMovements = AccountMovement::whereBetween('date', [$startDate, $endDate])
    ->with('transaction')
    ->get();

    //Affect all Cash Sales and uses Cash Accounts
    foreach ($accMovements->groupBy('chart_id') as $groupedByAccount) {
      $value = 0;

      //calculate value by currency. fx. TODO, Include Rounding depending on Main Curreny from Taxpayer Country.
      foreach ($groupedByAccount->groupBy('rate') as $groupedByRate) {
        $value += $groupedByRate->sum('credit') * $groupedByRate->first()->rate;
      }

      if ($value > 0) {
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

    //Affect all Credit Sales and uses Credit Accounts
    foreach ($accMovements->transaction->groupBy('customer_id') as $groupedByInvoice) {
      $value = 0;

      //calculate value by currency. fx. TODO, Include Rounding depending on Main Curreny from Taxpayer Country.
      foreach ($groupedByInvoice->groupBy('rate') as $groupedByRate) {
        $value += $groupedByRate->sum('credit') * $groupedByRate->first()->rate;
      }

      if ($value > 0) {
        //Check for Account Receivables used.
        $chart = $ChartController->createIfNotExists_AccountsReceivables($this->taxPayer, $this->cycle, $groupedByInvoice->first()->partner_taxid);

        $detail = new JournalDetail();
        $detail->debit = $value;
        $detail->credit = 0;
        $detail->chart_id = $chart->id;
        $detail->journal_id = $journal->id;
        $detail->save();
      }
    }

    foreach ($accMovements as $mov) {
      $mov->setStatus('Accounted');

      $journalAccountMovement = new JournalAccountMovement();
      $journalAccountMovement->journal_id = $journal->id;
      $journalAccountMovement->account_movement_id = $mov->id;
      $journalAccountMovement->save();
    }
  }
}
