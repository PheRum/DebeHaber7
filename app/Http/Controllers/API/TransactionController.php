<?php

namespace App\Http\Controllers\API;

use App\Taxpayer;
use App\Chart;
use App\ChartVersion;
use App\Currency;
use App\CurrencyRate;
use App\Cycle;
use App\ChartAlias ;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use DB;
use Auth;

class TransactionController extends Controller
{
    public function start(Request $request) {
        $transactionData = array();
        $cycle = null;

        $chunkedData = $request;

        if (isset($chunkedData))
        {
            $data = collect($chunkedData);
            $groupData = $data->groupBy(function($q) { return Carbon::parse($q["Date"])->format('Y'); });

            //groupby function group by year.
            foreach ($groupData as $groupedRow)
            {
                if ($groupedRow->first()['Type'] == 4 || $groupedRow->first()['Type'] == 5)
                { $taxPayer = $this->checkTaxPayer($groupedRow->first()['SupplierTaxID'], $groupedRow->first()['SupplierName']); }
                else if($groupedRow->first()['Type'] == 3 || $groupedRow->first()['Type'] == 1)
                { $taxPayer = $this->checkTaxPayer($groupedRow->first()['CustomerTaxID'], $groupedRow->first()['CustomerName']); }

                //check and create cycle
                $firstDate = Carbon::parse($groupedRow->first()["Date"]);

                //No need to run this query for each invoice, just check if the date is in between.
                $cycle = Cycle::where('start_date', '<=', $firstDate)
                ->where('end_date', '>=', $firstDate)
                ->where('taxpayer_id', $taxPayer->id)
                ->first();

                if (!isset($cycle))  {
                    $cycle = $this->checkCycle($taxPayer,$firstDate);
                }


                $i = 0;
                foreach ($groupedRow as $data)
                {
                    try {
                        $data = $this->processTransaction($data, $taxPayer, $cycle);
                        $data["Message"] = "Success";
                        $transactionData[$i] = $data;
                        $i = $i + 1;
                    } catch (\Exception $e) {
                        $data["Message"] = "Error loading transaction: " .$e ;
                        $transactionData[$i] = $data;
                    }
                }
            }
        }

        return response()->json($transactionData);
    }

    public function processTransaction($data, Taxpayer $taxPayer, Cycle $cycle) {

        // 4 & 5, then is Sales or Credit Note. So Customer is our client, and current Taxpayer is Supplier
        if ($data['Type'] == 4 || $data['Type'] == 5) {
            $customer = $this->checkTaxPayer($data['CustomerTaxID'], $data['CustomerName']);
            $supplier = $taxPayer;
        }
        //If type 1 & 3, then it is Purchase or Debit Note. So we should bring our supplier and current Taxpayer is Customer
        else if($data['Type'] == 1 || $data['Type'] == 3) {
            $customer = $taxPayer;
            $supplier = $this->checkTaxPayer($data['SupplierTaxID'], $data['SupplierName']);
        }
        //TODO. There should be logic that checks if RefID for this Taxpayer is already int the system. If so, then only update, or else create.
        //Im not too happy with this code since it will call db every time there is a new invoice. Maybe there is a better way, or simply remove this part and insert it again.


        $transaction = Transaction::
        where('number', $data['Number'])
        ->where('type', $data['Type'])
        ->where('customer_id', $customer->id)
        ->where('supplier_id', $supplier->id)
        ->first() ?? new Transaction();

        $transaction->type = $data['Type'];
        //$transaction->is_impex = $data['IsImpex'] ? 1 : 0;
        $transaction->customer_id = $customer->id;
        $transaction->supplier_id = $supplier->id;

        //TODO, this is not enough. Remove Cycle, and exchange that for Invoice Date. Since this will tell you better the exchange rate for that day.
        $transaction->currency_id = $this->checkCurrency($data['CurrencyCode'], $taxPayer);

        if ($data['CurrencyRate'] ==  '') {
            $transaction->rate = $this->checkCurrencyRate($transaction->currency_id, $taxPayer, $data['Date']) ?? 1;
        } else {
            $transaction->rate = $data['CurrencyRate'];
        }

        $transaction->payment_condition = $data['PaymentCondition'];

        // //TODO, do not ask if chart account id is null.
        // if ($data['AccountName'] != null && $transaction->payment_condition == 0)
        // {
        //     $transaction->chart_account_id = $this->checkChartAccount($data['AccountName'], $taxPayer, $cycle);
        // }

        //You may need to update the code to a Carbon nuetral. Check this, I may be wrong.

        $transaction->date = $this->convert_date($data['Date']);
        $transaction->number = $data['Number'];
        $transaction->code = $data['Code'] != '' ? $data['Code'] : null;
        $transaction->code_expiry = $data['CodeExpiry'] != '' ? $this->convert_date($data['CodeExpiry'])  : null;
        $transaction->comment = $data['Comment'];
        $transaction->save();

        //Process details of the invoice.
        $this->processDetail(
            collect($data['Details']), $transaction->id, $taxPayer, $cycle, $data['Type']
        );

        $data['cloud_id'] = $transaction->id;

        return $data;
    }

    public function processDetail($details, $transaction_id, Taxpayer $taxPayer, Cycle $cycle, $type) {
        //???
        $totalDiscount = $details->where('Value', '<', 0)->sum('Value');
        $totalValue = $details->where('Value', '>', 0)->sum('Value') != 0 ?
        $details->where('Value', '>', 0)->sum('Value') : 1;

        //TODO to reduce data stored, group by VAT and Chart Type.
        //If 5 rows can be converted into 1 row it is better for our system's health and reduce server load.
        foreach ($details->groupBy('VATPercentage') as $groupedRowsByVat)
        {

            foreach ($groupedRowsByVat->groupBy('Type') as $groupedRowsByType)
            {

                if($groupedRowsByType[0]['Value'] > 0)
                {
                    //Code for Row Level Discounts in certain transactions
                    $discountOnRow = 0;
                    if ($totalDiscount > 0)
                    {
                        $percentage = $value / $totalValue;
                        $discountOnRow = $percentage * $totalDiscount;
                    }

                    $chart_id = $this->checkChart($groupedRowsByType[0]['Type'], $groupedRowsByType[0]['Name'], $taxPayer, $cycle, $type);

                    $detail = TransactionDetail::where('chart_id' ,$chart_id)->where('transaction_id' ,$transaction_id)->first()?? new TransactionDetail();
                    
                    $detail->transaction_id = $transaction_id;
                    $detail->chart_id =$chart_id;

                    if ($type == 1 || $type == 5)
                    { $detail->chart_vat_id = $this->checkCreditVAT($groupedRowsByType[0]['VATPercentage'], $taxPayer, $cycle); }
                    elseif ($type == 3 || $type == 4)
                    { $detail->chart_vat_id = $this->checkDebitVAT($groupedRowsByType[0]['VATPercentage'], $taxPayer, $cycle); }

                    $detail->value = $groupedRowsByType->sum('Value') - $discountOnRow;

                    $detail->save();
                }
            }
        }
    }
}
