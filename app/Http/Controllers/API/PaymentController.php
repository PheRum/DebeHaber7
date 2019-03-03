<?php

namespace App\Http\Controllers\API;

use App\Taxpayer;
use App\Cycle;
use App\Transaction;
use App\AccountMovement;
use App\Http\Controllers\ChartController;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function start(Request $request)
    {
        $cycle = null;
        $chunkedData = $request;

        if (isset($chunkedData)) {
            $data = collect($chunkedData);

            $groupData = $data->groupBy(function ($q) {
                return Carbon::parse($q["Date"])->format('Y');
            });
            $i = 0;
            //groupby function group by year.
            foreach ($groupData as $groupedRow) {

                if ($groupedRow->first()['Type'] == 2) {
                    $taxPayer = $this->checkTaxPayer($groupedRow->first()['SupplierTaxID'], $groupedRow->first()['SupplierName']);
                } else if ($groupedRow->first()['Type'] == 1) {
                    $taxPayer = $this->checkTaxPayer($groupedRow->first()['CustomerTaxID'], $groupedRow->first()['CustomerName']);
                }

                //check and create cycle
                $firstDate = Carbon::parse($groupedRow->first()["Date"]);
                $cycle = Cycle::My($taxPayer, $firstDate)->first();
                if (!isset($cycle)) {
                    $cycle = $this->checkCycle($taxPayer, $firstDate);
                }

                $startDate = $cycle->start_date;
                $endDate = $cycle->end_date;

                foreach ($groupedRow as $data) {

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
        //Payment Made (Account Payable)
        if ($data['Type'] == 1) {
            $transaction = Transaction::where('payment_condition', '>', 0)
                ->where('taxpayer_id', $taxPayer->id)
                ->whereDate('date', $this->convert_date($data['InvoiceDate']))
                ->where('number', $data['InvoiceNumber'])
                ->where('type', 1)
                ->first();

            if ($transaction != null) {
                $accMovement = $this->processPayments($data, $taxPayer, $transaction, $cycle);
                $data['cloud_id'] = $accMovement->id;
            }
        } else if ($data['Type'] == 2) {
            //Payment Received (Account Receivables)
            $transaction = Transaction::where('payment_condition', '>', 0)
                ->where('taxpayer_id', $taxPayer->id)
                ->whereDate('date', $this->convert_date($data['InvoiceDate']))
                ->where('number', $data['InvoiceNumber'])
                ->where('type', 3)
                ->first();

            //TODO, we should only process payments of invoices that are on credit. All invoices that are on cash, should be generalized and summed by their account.

            if ($transaction != null) {
                $accMovement = $this->processPayments($data, $taxPayer, $transaction, $cycle);
                $data['cloud_id'] = $accMovement->id;
            }
        }

        //Return account movement if not null.
        return $data;
    }

    public function processPayments($data, $taxPayer, $invoice, $cycle)
    {
        $accMovement = AccountMovement::where('taxpayer_id', $taxPayer->id)
            ->where('transaction_id', $invoice->id)
            ->where('date', $this->convert_date($data['Date']))
            ->where('comment', $data['Comment'])
            ->first() ?? new AccountMovement();

        //Get Payment Type. 0=Normal, 1=CreditNote, 2=VATWitholding
        $payentType = $data['PaymentType'];

        if ($payentType == 0) {
            $chartID = $this->checkChartAccount($data['AccountName'], $taxPayer, $cycle);
        } else if ($payentType == 1) {
            $chartController = new ChartController();
            //get accounts pending for customers and suppliers
            if ($data['Type'] == 1) {
                $chartID = $chartController->createIfNotExists_AccountsReceivables($taxPayer, $cycle, $invoice->partner_taxid, $invoice->partner_name)->id;
            } else {
                $chartID = $chartController->createIfNotExists_AccountsPayable($taxPayer, $cycle, $invoice->partner_taxid, $invoice->partner_name)->id;
            }
        } else if ($payentType == 2) {
            $chartController = new ChartController();
            //get accounts pending for customers and suppliers
            if ($data['Type'] == 1) {
                $chartID = $chartController->createIfNotExists_VATWithholdingReceivables($taxPayer, $cycle, $invoice->partner_name)->id;
            } else {
                $chartID = $chartController->createIfNotExists_VATWithholdingPayables($taxPayer, $cycle, $invoice->partner_name)->id;
            }
        }

        $accMovement->chart_id = $chartID;
        $accMovement->taxpayer_id = $taxPayer->id;
        $accMovement->transaction_id = $invoice->id;
        $accMovement->currency = $this->checkCurrency($data['CurrencyCode'], $taxPayer);

        //Check currency rate based on date. if nothing found use default from api. TODO this should be updated to buy and sell rates.
        if ($data['CurrencyRate'] ==  '') {
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

    public function processPaymentsWithoutTransaction($data, $taxPayer, $cycle)
    {
        //TODO: get PartnerData from $data.
        $partnerTaxID = $data['PartnerTaxId'];
        $partnerName = $data['PartnerName'];
        $accMovement = new AccountMovement();
        //Get Payment Type. 0 = Normal, 1 = CreditNote, 2 = VATWitholding
        $payentType = $data['PaymentType'];

        if ($payentType == 0) {
            $chartID = $this->checkChartAccount($data['AccountName'], $taxPayer, $cycle);
        } else if ($payentType == 1) {
            $chartController = new ChartController();
            //get accounts pending for customers and suppliers
            if ($data['Type'] == 1) {
                $chartID = $chartController->createIfNotExists_AccountsReceivables($taxPayer, $cycle, $partnerTaxID, $partnerName);
            } else {
                $chartID = $chartController->createIfNotExists_AccountsPayable($taxPayer, $cycle, $partnerTaxID, $partnerName);
            }
        } else if ($payentType == 2) {
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
        // $accMovement->partner_name = $partner->name;
        // $accMovement->partner_taxid = $partner->taxid;
        $accMovement->currency = $this->checkCurrency($data['CurrencyCode'], $taxPayer);

        //Check currency rate based on date. if nothing found use default from api. TODO this should be updated to buy and sell rates.
        if ($data['CurrencyRate'] ==  '') {
            $currency_id = $this->checkCurrency($data['CurrencyCode'], $taxPayer);
            $accMovement->rate = $this->checkCurrencyRate($currency_id, $taxPayer, $data['Date']) ?? 1;
        } else {
            $accMovement->rate = $data['CurrencyRate'] ?? 1;
        }

        $accMovement->date = $this->convert_date($data['Date']);
        //based on invoice type choose if its credit or debit.
        $accMovement->credit = $data['Credit'];
        $accMovement->debit = $data['Debit'];

        $accMovement->comment = $data['Comment'];

        $accMovement->save();

        return $accMovement;
    }
}
