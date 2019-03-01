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
use App\AccountMovement;
use App\TransactionDetail;
use App\Http\Controllers\ChartController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use DB;
use Auth;

class PaymentController extends Controller
{
    public function start(Request $request)
    {
        $cycle = null;
        $chunkedData = $request;

        if (isset($chunkedData))
        {
            $data = collect($chunkedData);

            $groupData = $data->groupBy(function($q) { return Carbon::parse($q["Date"])->format('Y'); });
            $i = 0;
            //groupby function group by year.
            foreach ($groupData as $groupedRow)
            {
                if ($groupedRow->first()['Type'] == 2)
                { $taxPayer = $this->checkTaxPayer($groupedRow->first()['SupplierTaxID'], $groupedRow->first()['SupplierName']); }
                else if($groupedRow->first()['Type'] == 1)
                { $taxPayer = $this->checkTaxPayer($groupedRow->first()['CustomerTaxID'], $groupedRow->first()['CustomerName']); }

                $firstDate = Carbon::parse($groupedRow->first()["Date"]);
                //No need to run this query for each invoice, just check if the date is in between.
                $cycle = Cycle::where('start_date', '<=', $firstDate)
                ->where('end_date', '>=',$firstDate)
                ->where('taxpayer_id', $taxPayer->id)
                ->first();

                if (!isset($cycle)) {
                    $cycle = $this->checkCycle($taxPayer,$firstDate);
                }


                $startDate = $cycle->start_date;
                $endDate = $cycle->end_date;

                foreach ($groupedRow as $data)
                {
                    $accMovement = $this->processTransaction($data, $taxPayer, $cycle);
                    $accMovement["Message"] = "Success";
                    $request[$i] = $accMovement;
                    $i = $i + 1;

                }
            }

        }

        return response()->json($request);
    }

    public function processTransaction($data, Taxpayer $taxPayer, Cycle $cycle)
    {

        if ($data['Type'] == 1) //Payment Made (Account Payable)
        {

            $customer = $taxPayer;

            $supplier = $this->checkTaxPayer($data['SupplierTaxID'], $data['SupplierName']);


            $transaction = Transaction::where('partner_taxid', $supplier->taxid)
            ->where('taxpayer_id', $customer->id)
            ->whereDate('date', $this->convert_date($data['InvoiceDate']))
            ->where('number', $data['InvoiceNumber'])
            ->whereIn('type', [1, 2])
            ->first();

            if ($transaction != null)
            {
                if ($transaction->payment_condition>0)
                {
                    $accMovement = $this->processPayments($data, $taxPayer, $transaction, $cycle,$supplier);
                }
                else
                {
                    return $data;
                }

            }
            else
            {
            
                $accMovement = $this->processPaymentsWithoutTransaction($data, $taxPayer, $supplier, $cycle);
            }
        }
        else if ($data['Type'] == 2) //Payment Received (Account Receivables)
        {
            $customer = $this->checkTaxPayer($data['CustomerTaxID'], $data['CustomerName']);
            $supplier = $taxPayer;

            $transaction = Transaction::where('taxpayer_id', $supplier->id)
            ->where('partner_taxid', $customer->taxid)
            ->whereDate('date', $this->convert_date($data['InvoiceDate']))
            ->where('number', $data['InvoiceNumber'])
            ->where('type', 4)
            ->first();

            if ($transaction != null)
            {
                if ($transaction->payment_condition > 0)
                {
                    $accMovement = $this->processPayments($data, $taxPayer, $transaction, $cycle,$customer);
                }
                else
                {
                    return $data;
                }
            }
            else
            {

                $accMovement = $this->processPaymentsWithoutTransaction($data, $taxPayer, $customer, $cycle);
            }
        }

        $data['cloud_id'] = $accMovement->id;
        //Return account movement if not null.
        return $data;
    }

    public function processPayments($data, $taxPayer, $invoice, $cycle,$partner)
    {
        $accMovement = AccountMovement::where('taxpaer_id', $taxPayer->id)
        ->where('transaction_id', $invoice->id)
        ->where('date', $this->convert_date($data['Date']))
        ->where('comment', $data['Comment'])
        ->first() ?? new AccountMovement();

        //Get Payment Type. 0=Normal, 1=CreditNote, 2=VATWitholding
        $payentType = $data['PaymentType'];

        if ($payentType == 0)
        {
            $chartID = $this->checkChartAccount($data['AccountName'], $taxPayer, $cycle);
        }
        else if ($payentType == 1)
        {
            $chartController = new ChartController();
            //get accounts pending for customers and suppliers
            if ($data['Type'] == 1) {
                $chartID = $chartController->createIfNotExists_AccountsReceivables($taxPayer, $cycle, $invoice->partner_taxid);
            } else {
                $chartID = $chartController->createIfNotExists_AccountsPayable($taxPayer, $cycle, $invoice->partner_taxid);
            }
        }
        else if ($payentType == 2)
        {
            $chartController = new ChartController();
            //get accounts pending for customers and suppliers
            if ($data['Type'] == 1) {
                $chartID = $chartController->createIfNotExists_VATWithholdingReceivables($taxPayer, $cycle);
            } else {
                $chartID = $chartController->createIfNotExists_VATWithholdingPayables($taxPayer, $cycle);
            }
        }

        $accMovement->chart_id = $chartID;
        $accMovement->partner_name = $data['PartnerName'];
        $accMovement->partner_taxid = $data['PartnerTaxId'];
        $accMovement->taxpayer_id = $taxPayer->id;
        $accMovement->transaction_id = $invoice->id;
        $accMovement->currency_id = $this->checkCurrency($data['CurrencyCode'], $taxPayer);

        //Check currency rate based on date. if nothing found use default from api. TODO this should be updated to buy and sell rates.
        if ($data['CurrencyRate'] ==  '' ) {
            $accMovement->rate = $this->checkCurrencyRate($accMovement->currency_id, $taxPayer, $data['Date']) ?? 1;
        } else {
            $accMovement->rate = $data['CurrencyRate'] ?? 1;
        }

        $accMovement->date = $this->convert_date($data['Date']);
        //based on invoice type choose if its credit or debit.
        $accMovement->credit = $invoice->type == 4 ?  $data['Credit'] : 0;
        $accMovement->debit = $invoice->type == 1 ? $data['Debit'] : 0;
        $accMovement->comment = $data['Comment'];

        $accMovement->save();

        return $accMovement;
    }

    public function processPaymentsWithoutTransaction($data, $taxPayer,$partner, $cycle)
    {

        $accMovement = new AccountMovement();

        //Get Payment Type. 0=Normal, 1=CreditNote, 2=VATWitholding
        $payentType = $data['PaymentType'];

        if ($payentType == 0)
        {
            $chartID = $this->checkChartAccount($data['AccountName'], $taxPayer, $cycle);
        }
        else if ($payentType == 1)
        {
            $chartController = new ChartController();
            //get accounts pending for customers and suppliers
            if ($data['Type'] == 1) {
                $chartID = $chartController->createIfNotExists_AccountsReceivables($taxPayer, $cycle, $partner->id);
            } else {
                $chartID = $chartController->createIfNotExists_AccountsPayable($taxPayer, $cycle, $partner->id);
            }
        }
        else if ($payentType == 2)
        {
            $chartController = new ChartController();
            //get accounts pending for customers and suppliers
            if ($data['Type'] == 1) {
                $chartID = $chartController->createIfNotExists_VATWithholdingReceivables($taxPayer, $cycle);
            } else {
                $chartID = $chartController->createIfNotExists_VATWithholdingPayables($taxPayer, $cycle);
            }

        }

        $accMovement->chart_id = $chartID->id;
        $accMovement->taxpayer_id = $taxPayer->id;
        $accMovement->partner_name = $partner->name;
        $accMovement->partner_taxid = $partner->taxid;
        $accMovement->currency = $this->checkCurrency($data['CurrencyCode'], $taxPayer);

        //Check currency rate based on date. if nothing found use default from api. TODO this should be updated to buy and sell rates.
        if ($data['CurrencyRate'] ==  '' ) {
            $currency_id = $this->checkCurrency($data['CurrencyCode'], $taxPayer);
            $accMovement->rate = $this->checkCurrencyRate($currency_id, $taxPayer, $data['Date']) ?? 1;
        } else {
            $accMovement->rate = $data['CurrencyRate'] ?? 1;
        }

        $accMovement->date = $this->convert_date($data['Date']);
        //based on invoice type choose if its credit or debit.
        $accMovement->credit = $data['Credit'];
        $accMovement->debit = $data['Debit'] ;

        $accMovement->comment = $data['Comment'];

        $accMovement->save();

        return $accMovement;
    }
}
