<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Taxpayer;
use App\Transaction;
use App\TransactionDetail;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Taxpayer $taxPayer)
    {
        $transaction = Transaction::firstOrNew(['id' => $request->id]);

        $transaction->taxpayer_id = $taxPayer->id;
        $transaction->partner_name = $request->partner_name;
        $transaction->partner_taxid = $request->partner_taxid;

        $transaction->document_id = $request->document_id > 0 ? $request->document_id : null;

        $transaction->currency = $request->currency;
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
