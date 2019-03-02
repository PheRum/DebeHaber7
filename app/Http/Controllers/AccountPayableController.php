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

class AccountPayableController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Taxpayer $taxPayer, Cycle $cycle)
  {
    return GeneralResource::collection(
      Transaction::MyPurchases()
      ->with('currency')
      ->with('details')
      ->with('customer')
      ->with('accountMovements')
      ->where('payment_condition', '>', 0)
      ->paginate(50)
    );
  }

  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    if ($request->payment_value > 0) {
      $accountMovement = new AccountMovement();
      $accountMovement->taxpayer_id = $request->taxpayer_id;
      $accountMovement->chart_id = $request->chart_account_id;
      $accountMovement->date = $request->date;

      $accountMovement->transaction_id = $request->id != '' ? $request->id : null;
      $accountMovement->currency_id = $request->currency_id;
      $accountMovement->rate = $request->rate ?? 1;
      $accountMovement->debit = $request->payment_value != '' ? $request->payment_value : 0;
      $accountMovement->comment = $request->comment;

      $accountMovement->save();

      return response()->json('Ok', 200);
    }

    return response()->json('No Value', 403);
  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param  \App\AccountMovement  $accountMovement
  * @return \Illuminate\Http\Response
  */
  public function show(Taxpayer $taxPayer, Cycle $cycle,$transactionId)
  {
        return new GeneralResource(
      AccountMovement::where('transaction_id', $transactionId)
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
  { }

  public function generate_Journals($startDate, $endDate, $taxPayer, $cycle)
  {
    \DB::connection()->disableQueryLog();

    $queryAccountMovements = AccountMovement::My($startDate, $endDate, $taxPayer->id);

    if ($queryAccountMovements->where('journal_id', '!=', null)->count() > 0) {
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
    ->whereHas('transaction', function ($q) use ($taxPayer) {
      $q->where('customer_id', '=', $taxPayer->id)
      ->where('payment_condition', '>', 0);
    })->get();

    //run code for credit purchase (insert detail into journal)
    foreach ($listOfPayables as $row) {
      $value = $row->debit * $row->rate;

      if ($value == 0) {
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
        $detail->chart_id = $chartController->createIfNotExists_IncomeFromFX($taxPayer, $cycle)->id;
        $journal->details()->save($detail);
      } else if ($paymentRate > $invoiceRate) //Loss by Exchange Rante Difference
      {
        $detail = new \App\JournalDetail();
        $detail->credit = 0;
        $detail->debit = $row->credit * $rateDifference;
        $detail->chart_id = $chartController->createIfNotExists_ExpenseFromFX($taxPayer, $cycle)->id;
        $journal->details()->save($detail);
      }
    }

    if ($journal->details()->count() == 0) {
      $journal->details()->delete();
      $journal->delete();
    }

    AccountMovement::whereIn('id', $queryAccountMovements->pluck('id'))
    ->update(['journal_id' => $journal->id]);
  }
}
