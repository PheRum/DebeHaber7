<?php

namespace App\Http\Controllers\API;

use App\Taxpayer;
use App\Cycle;
use App\Impex;
use App\ImpexExpense;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ImpexController extends Controller
{
    public function start(Request $request)
    {
        $impexData = array();
        $cycle = null;

        $chunkedData = $request;

        if (isset($chunkedData)) {
            $data = collect($chunkedData);
            $groupData = $data->groupBy(function ($q) {
                return Carbon::parse($q["Date"])->format('Y');
            });

            //groupby function group by year.
            foreach ($groupData as $groupedRow) {
                if ($groupedRow->first()['Type'] == 4 || $groupedRow->first()['Type'] == 5) {
                    $taxPayer = $this->checkTaxPayer($groupedRow->first()['SupplierTaxID'], $groupedRow->first()['SupplierName']);
                } else if ($groupedRow->first()['Type'] == 3 || $groupedRow->first()['Type'] == 1) {
                    $taxPayer = $this->checkTaxPayer($groupedRow->first()['CustomerTaxID'], $groupedRow->first()['CustomerName']);
                }

                //check and create cycle
                $firstDate = Carbon::parse($groupedRow->first()["Date"]);
                $cycle = Cycle::My($taxPayer, $firstDate)->first();
                if (!isset($cycle)) {
                    $cycle = $this->checkCycle($taxPayer, $firstDate);
                }

                $i = 0;
                foreach ($groupedRow as $data) {
                    try {
                        $data = $this->processImpex($data, $taxPayer, $cycle);
                        $data["Message"] = "Success";
                        $impexData[$i] = $data;
                        $i = $i + 1;
                    } catch (\Exception $e) {
                        $data["Message"] = "Error loading Impex: " . $e;
                        $impexData[$i] = $data;
                    }
                }
            }
        }

        return response()->json($impexData);
    }

    public function processImpex($data, Taxpayer $taxPayer, Cycle $cycle)
    {

        $impex = Impex::where('code', $data['Code'])
            ->where('taxpayer_id', $taxPayer->id)
            ->first() ?? new Impex();

        $impex->taxpayer = $taxPayer->id;
        $impex->code = $data['Code'];
        $impex->impex_id = $data['IsImpex'];
        $impex->type = substr($data['Incoterm'], 0, 4);
        $impex->currency_id = $this->checkCurrency($data['CurrencyCode'], $taxPayer);

        if ($data['CurrencyRate'] ==  '' | $data['CurrencyRate'] == 0) {
            $impex->rate = $this->checkCurrencyRate($impex->currency_id, $taxPayer, $data['Date']) ?? 1;
        } else {
            $impex->rate = $data['CurrencyRate'];
        }

        $impex->save();

        //Assign invoices . . .
        $invoices = collect($data['Invoices']);

        foreach ($invoice as $invoices) {
            $transaction = $this->processInvoice($invoice, $impex, $taxPayer, $cycle);
            $invoice['cloud_id'] = $transaction->id;
        }

        //Assign expenses . . .
        $expenses = collect($data['Expenses']);

        foreach ($expense as $expenses) {
            $impexExpense = ImpexExpense::where('id', $invoice)->first() ?? new ImpexExpense();

            if ($expense[0]['Value'] > 0) {
                //Code for Row Level Discounts in certain transactions
                $discountOnRow = 0;
                if ($totalDiscount > 0) {
                    $percentage = $value / $totalValue;
                    $discountOnRow = $percentage * $totalDiscount;
                }

                $chart_id = $this->checkChart($groupedRowsByType[0]['Type'], $groupedRowsByType[0]['Name'], $taxPayer, $cycle, $type);

                $detail = TransactionDetail::firstOrNew(['chart_id' => $chart_id]);

                $detail->transaction_id = $transaction_id;
                $detail->chart_id = $chart_id;

                if ($type == 1 || $type == 5) {
                    $detail->chart_vat_id = $this->checkCreditVAT($groupedRowsByType[0]['VATPercentage'], $taxPayer, $cycle);
                } elseif ($type == 3 || $type == 4) {
                    $detail->chart_vat_id = $this->checkDebitVAT($groupedRowsByType[0]['VATPercentage'], $taxPayer, $cycle);
                }

                $detail->value = $groupedRowsByType->sum('Value') - $discountOnRow;
                $detail->save();
            }
        }

        return $data;
    }

    public function processInvoice($data, Impex $impex, Taxpayer $taxPayer, Cycle $cycle)
    {

        //TODO if countries like Paraguay, the customer or supplier (partner) will enter.
        // be careful not to add partners as they are not used by companies.
        //Maybe set taxpayerID assigned to make ownership.

        // 4 & 5, then is Sales or Credit Note. So Customer is our client, and current Taxpayer is Supplier
        if ($data['Type'] == 4 || $data['Type'] == 5) {
            $customer = $this->checkTaxPayer($data['CustomerTaxID'], $data['CustomerName']);
            $supplier = $taxPayer;
        }
        //If type 1 & 3, then it is Purchase or Debit Note. So we should bring our supplier and current Taxpayer is Customer
        else if ($data['Type'] == 1 || $data['Type'] == 3) {
            $customer = $taxPayer;
            $supplier = $this->checkTaxPayer($data['SupplierTaxID'], $data['SupplierName']);
        }

        $transaction = Transaction::where('number', $invoice['Number'])
            ->where('type', $data['Type'])
            ->where('customer_id', $customer->id)
            ->where('supplier_id', $supplier->id)
            ->first() ?? new Transaction();

        $transaction->type = $data['Type'];
        $transaction->customer_id = $customer->id;
        $transaction->supplier_id = $supplier->id;
        $transaction->impex_id = $impex->id;

        //TODO, this is not enough. Remove Cycle, and exchange that for Invoice Date. Since this will tell you better the exchange rate for that day.
        $transaction->currency_id = $this->checkCurrency($data['CurrencyCode'], $taxPayer);

        if ($data['CurrencyRate'] ==  '') {
            $transaction->rate = $this->checkCurrencyRate($transaction->currency_id, $taxPayer, $data['Date']) ?? 1;
        } else {
            $transaction->rate = $data['CurrencyRate'];
        }

        $transaction->payment_condition = $data['PaymentCondition'];
        $transaction->date = $this->convert_date($data['Date']);
        $transaction->number = $data['Number'];
        $transaction->code = $data['Code'] != '' ? $data['Code'] : null;
        $transaction->code_expiry = $data['CodeExpiry'] != '' ? $this->convert_date($data['CodeExpiry'])  : null;
        $transaction->comment = $data['Comment'];
        $transaction->save();

        //Process details of the invoice.
        $this->processDetail(
            collect($data['Details']),
            $transaction->id,
            $taxPayer,
            $cycle,
            $data['Type']
        );

        return $transaction;
    }

    public function processDetail($details, $transaction_id, Taxpayer $taxPayer, Cycle $cycle, $type)
    {
        //In case negative values find their way to DH, process negatives as discount and reduce from positive values.
        $totalDiscount = $details->where('Value', '<', 0)->sum('Value');
        $totalValue = $details->where('Value', '>', 0)->sum('Value') >= 0 ? $details->where('Value', '>', 0)->sum('Value') : 1;

        //TODO to reduce data stored, group by VAT and Chart Type.
        //If 5 rows can be converted into 1 row it is better for our system's health and reduce server load.
        foreach ($details->groupBy('VATPercentage') as $groupedRowsByVat) {
            foreach ($groupedRowsByVat->groupBy('Type') as $groupedRowsByType) {
                if ($groupedRowsByType[0]['Value'] > 0) {
                    //Code for Row Level Discounts in certain transactions
                    $discountOnRow = 0;
                    if ($totalDiscount > 0) {
                        $percentage = $value / $totalValue;
                        $discountOnRow = $percentage * $totalDiscount;
                    }

                    $chart_id = $this->checkChart($groupedRowsByType[0]['Type'], $groupedRowsByType[0]['Name'], $taxPayer, $cycle, $type);

                    $detail = TransactionDetail::firstOrNew(['chart_id' => $chart_id]);

                    $detail->transaction_id = $transaction_id;
                    $detail->chart_id = $chart_id;

                    if ($type == 1 || $type == 5) {
                        $detail->chart_vat_id = $this->checkCreditVAT($groupedRowsByType[0]['VATPercentage'], $taxPayer, $cycle);
                    } elseif ($type == 3 || $type == 4) {
                        $detail->chart_vat_id = $this->checkDebitVAT($groupedRowsByType[0]['VATPercentage'], $taxPayer, $cycle);
                    }

                    $detail->value = $groupedRowsByType->sum('Value') - $discountOnRow;

                    $detail->save();
                }
            }
        }
    }
}
