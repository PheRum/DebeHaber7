<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\View2Excel;
use App\Taxpayer;
use App\Transaction;
use App\TransactionDetail;
use App\Cycle;
use App\Journal;
use App\Chart;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PDF;
use DB;

class ReportController extends Controller
{
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return view('reports/index');
    }

    public function chartOfAccounts(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->chartQuery($taxPayer, $cycle, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.chart_of_accounts',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]),
                __('accounting.ChartOfAccounts') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else if($e == 'p')
            {
                $pdf = PDF::loadView('reports.accounting.chart_of_accounts',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]);
                return $pdf->download(__('accounting.ChartOfAccounts') . ' | ' . $startDate . '-' . $endDate . '.pdf');
            }
            else
            {
                return view('reports/accounting/chart_of_accounts')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function subLedger(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate)->sortBy('date');

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.ledger-sub',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('accounting.SubLedger') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else if($e == 'p')
            {
                $pdf = PDF::loadView('reports.accounting.ledger-sub',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]);
                return $pdf->download(__('accounting.SubLedger') . ' | ' . $startDate . '-' . $endDate . '.pdf');
            }
            else
            {
                return view('reports/accounting/ledger-sub')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function ledger(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate)->sortBy('date');;

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.ledger',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]),
                __('accounting.Ledger') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else if($e == 'p')
            {
                $pdf = PDF::loadView('reports.accounting.ledger',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]);
                return $pdf->download(__('accounting.Ledger') . ' | ' . $startDate . '-' . $endDate . '.pdf');
            }
            else
            {
                return view('reports/accounting/ledger')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function ledgerByMonth(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate)->sortBy('chartType');
            $period = CarbonPeriod::create($startDate, '1 month', $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.ledger-ByMonth',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate, 'period' => $period]),
                __('accounting.LedgerOf', ['attribute' => 'Month']) . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else if($e == 'p')
            {
                $pdf = PDF::loadView('reports.accounting.ledger-ByMonth',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]);
                return $pdf->download(__('accounting.LedgerOf', ['attribute' => 'Month']) . ' | ' . $startDate . '-' . $endDate . '.pdf');
            }
            else
            {
                return view('reports/accounting/ledger-ByMonth')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate)
                ->with('period', $period);
            }
        }
    }

    public function ledgerByCashAccount(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate)->where('chartType', 1)
            ->where('chartSubType', '=', 1)
            ->sortBy('chartName');

            $period = CarbonPeriod::create($startDate, '1 month', $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.ledger-ByCashAccounts',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate, 'period' => $period]),
                __('accounting.LedgerOf', ['attribute' => 'ByMonth']) . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else if($e == 'p')
            {
                $pdf = PDF::loadView('reports.accounting.ledger-ByCashAccounts',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]);
                return $pdf->download(__('accounting.LedgerOf', ['attribute' => 'ByMonth']) . ' | ' . $startDate . '-' . $endDate . '.pdf');
            }
            else
            {
                return view('reports.accounting.ledger-ByCashAccounts')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate)
                ->with('period', $period);
            }
        }
    }

    public function ledgerByReceivables(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate)
            ->where('chartType', '=', 1)
            ->where('chartSubType', '=', 5)
            ->sortBy('chartName');

            $period = CarbonPeriod::create($startDate, '1 month', $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.ledger-ByReceivables',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate, 'period' => $period]),
                __('accounting.LedgerOf', ['attribute' => 'AccountReceivables']) . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else if($e == 'p')
            {
                $pdf = PDF::loadView('reports.accounting.ledger-ByReceivables',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]);
                return $pdf->download(__('accounting.LedgerOf', ['attribute' => 'AccountReceivables']) . ' | ' . $startDate . '-' . $endDate . '.pdf');
            }
            else
            {
                return view('reports.accounting.ledger-ByReceivables')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate)
                ->with('period', $period);
            }
        }
    }

    public function ledgerByPayables(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate)->where('chartType', '=', 2)->where('chartSubType', '=', 1)->sortBy('chartName');
            $period = CarbonPeriod::create($startDate, '1 month', $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.ledger-ByPayables',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate, 'period' => $period]),
                __('accounting.LedgerOf', ['attribute' => 'ByMonth']) . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports.accounting.ledger-ByPayables')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate)
                ->with('period', $period);
            }
        }
    }

    public function ledgerByExpenses(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate)->where('chartType', '=', 5)->sortBy('chartSubType');
            $period = CarbonPeriod::create($startDate, '1 month', $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.ledger-ByExpenses',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate, 'period' => $period]),
                __('accounting.LedgerOf', ['attribute' => 'ByMonth']) . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports.accounting.ledger-ByExpenses')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate)
                ->with('period', $period);
            }
        }
    }

    public function balanceSheet(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $journals = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate);
            $charts = $this->chartQuery($taxPayer, $cycle, $startDate, $endDate);

            // Loop through Journal entries and add to chart balance
            foreach ($journals->groupBy('chart_id') as $journalGrouped)
            {
                $chart = $charts->where('id', $journalGrouped->first()->chart_id)->first();

                if ($chart != null)
                {
                    $chart->balance = $journalGrouped->sum('credit') - $journalGrouped->sum('debit');
                }
            }

            foreach ($charts as $chart)
            {
                $parentchart = $charts->where('id',$chart->parent_id)->first();
                if (isset($parentchart))
                {
                    $parentchart->balance = $parentchart->balance + $chart->balance;
                }
            }

            $data = $charts->sortBy('type')->sortBy('code');

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.balance-sheet',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('accounting.BalanceSheet') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/accounting/balance-sheet')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function balanceByMonth(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $journals = $this->journalQuery($taxPayer, $cycle->id, $startDate, $endDate)->whereIn('type', [1,2,3])->sortBy('chartName');
            $charts = $this->chartQuery($taxPayer, $cycle, $startDate, $endDate);
            $period = CarbonPeriod::create($startDate, '1 month', $endDate);

            // Loop through Journal entries and add to chart balance
            foreach ($journals->groupBy('chart_id') as $journalGrouped)
            {
                $chart = $charts->where('id', $journalGrouped->first()->chart_id)->first();

                if ($chart != null)
                {
                    $chart->balance = $journalGrouped->sum('credit') - $journalGrouped->sum('debit');
                }
            }

            foreach ($charts as $chart)
            {
                $parentchart = $charts->where('id',$chart->parent_id)->first();
                if (isset($parentchart))
                {
                    $parentchart->balance = $parentchart->balance + $chart->balance;
                }
            }

            $data = $charts->sortBy('type')->sortBy('code');

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.balance-byMonth',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('accounting.BalanceSheet') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/accounting/balance-byMonth')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate)
                ->with('period', $period);
            }
        }
    }

    public function balanceComparative(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->chartBalanceQuery($taxPayer, $cycle, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.accounting.balance-comparative',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('accounting.BalanceSheet(Comparative)') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/accounting/balance-comparative')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function purchases(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatPurchaseQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.PRY.purchases',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.Purchases') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/PRY/purchases')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function purchasesByVAT(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatPurchaseQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.PRY.purchases_byVAT',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.PurchaseByVAT') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/PRY/purchases_byVAT')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function purchasesByChart(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatPurchaseQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.PRY.purchases_byChart',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.PurchaseByChart') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/PRY/purchases_byChart')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function purchasesBySupplier(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatPurchaseQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.PRY.purchases_bySupplier',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.PurchaseBySuppliers') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/PRY/purchases_bySupplier')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function sales(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.PRY.sales',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.Sales') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/PRY/sales')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function salesByVAT(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.PRY.sales_byVAT',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.SalesByVAT') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/PRY/sales_byVAT')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function salesByChart(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.PRY.sales_byVat',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.SalesByChart') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/PRY/sales_byVat')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function salesByCustomer(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.PRY.sales_byCustomer',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.SalesByCustomer') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/PRY/sales_byCustomer')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function creditNotes(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatCreditNoteQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.commercial.credit-note',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.CreditNotes') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/commercial/credit-note')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function debitNotes(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatDebitNoteQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.commercial.debit-note',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.DebitNotes') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/commercial/debit-note')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function accountReceivable(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->vatSaleQuery($taxPayer, $startDate, $endDate)->where('payment_condition', '>', 0);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.commercial.account-receivable',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.AccountsReceivable') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/commercial/account-receivable')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function accountCustomer(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {

    }

    public function accountPayable(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {
        if (isset($taxPayer))
        {
            $data = $this->accPayablesQuery($taxPayer, $startDate, $endDate);

            if ($e == 'e')
            {
                return Excel::download(new View2Excel('reports.commercial.account-payable',
                ['data' => $data, 'header' => $taxPayer, 'strDate' => $startDate, 'endDate' => $endDate]), __('commercial.AccountsPayable') . ' | ' . $startDate . '-' . $endDate . '.xls');
            }
            else
            {
                return view('reports/commercial/account-payable')
                ->with('header', $taxPayer)
                ->with('data', $data)
                ->with('strDate', $startDate)
                ->with('endDate', $endDate);
            }
        }
    }

    public function accountSupplier(Taxpayer $taxPayer, Cycle $cycle, $startDate, $endDate, $e = '')
    {

    }

    public function vatPurchaseQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return TransactionDetail::join('charts', 'charts.id', 'transaction_details.chart_id')
        ->join('charts as vats', 'vats.id', 'transaction_details.chart_vat_id')
        ->join('transactions', 'transactions.id', 'transaction_details.transaction_id')
        ->join('taxpayers as supplier', 'transactions.supplier_id', 'supplier.id')
        ->join('taxpayers as customer', 'transactions.customer_id', 'customer.id')
        ->where('customer.id', $taxPayer->id)
        ->where('transactions.deleted_at', '=', null)
        ->whereIn('transactions.type', [1, 2])
        ->whereBetween('transactions.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->select('supplier.name as supplier',
        'supplier.taxid as supplier_code',
        'transactions.type',
        'transactions.id as purchaseID',
        'transactions.date',
        'transactions.code',
        'transactions.number',
        'transactions.payment_condition',
        'transactions.comment',
        'transactions.rate',
        'charts.name as costCenter',
        'vats.name as vat',
        'vats.coefficient',
        DB::raw('transactions.rate * if(transactions.type = 3, -transaction_details.value, transaction_details.value) as localCurrencyValue,
        (transactions.rate * if(transactions.type = 3, -transaction_details.value, transaction_details.value)) / (vats.coefficient + 1) as vatValue'
        )
        )
        ->orderBy('transactions.date', 'asc')
        ->orderBy('transactions.number', 'asc')
        ->get();
    }

    public function accPayablesQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return Transaction::MyPurchases()
        ->join('taxpayers', 'taxpayers.id', 'transactions.supplier_id')
        ->join('currencies', 'transactions.currency_id','currencies.id')
        ->join('transaction_details as td', 'td.transaction_id', 'transactions.id')
        ->leftJoin('account_movements', 'transactions.id', 'account_movements.transaction_id')
        ->where('transactions.customer_id', $taxPayer->id)
        ->where('transactions.payment_condition', '>', 0)
        ->whereBetween('transactions.date', [$startDate, $endDate])
        //->whereRaw('ifnull(sum(account_movements.debit), 0) < sum(td.value)')
        ->groupBy('transactions.id')
        ->select(DB::raw('max(transactions.id) as id'),
        DB::raw('max(taxpayers.name) as Supplier'),
        DB::raw('max(taxpayers.taxid) as SupplierTaxID'),
        DB::raw('max(currencies.code) as currency_code'),
        DB::raw('max(transactions.payment_condition) as payment_condition'),
        DB::raw('max(transactions.date) as date'),
        DB::raw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY) as code_expiry'),
        DB::raw('max(transactions.number) as number'),
        DB::raw('ifnull(sum(account_movements.debit/account_movements.rate), 0) as Paid'),
        DB::raw('sum(td.value/transactions.rate) as Value'))
        ->orderByRaw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY)', 'desc')
        ->orderByRaw('max(transactions.number)', 'desc')
        ->get();
    }

    public function vatSaleQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return TransactionDetail::join('charts', 'charts.id', 'transaction_details.chart_id')
        ->join('charts as vats', 'vats.id', 'transaction_details.chart_vat_id')
        ->join('transactions', 'transactions.id', 'transaction_details.transaction_id')
        ->join('taxpayers as supplier', 'transactions.supplier_id', 'supplier.id')
        ->join('taxpayers as customer', 'transactions.customer_id', 'customer.id')
        ->where('supplier.id', $taxPayer->id)
        ->where('transactions.type', 4)
        ->where('transactions.deleted_at', '=', null)
        ->whereBetween('transactions.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->select('customer.name as customer',
        'customer.taxid as customer_code',
        'transactions.type',
        'transactions.id as salesID',
        'transactions.date',
        'transactions.code',
        'transactions.number',
        'transactions.payment_condition',
        'transactions.comment',
        'transactions.rate',
        'charts.name as costCenter',
        'vats.name as vat',
        'vats.coefficient',
        DB::raw('transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value) as localCurrencyValue,
        (transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value)) / (vats.coefficient + 1) as vatValue')
        )
        ->orderBy('transactions.date', 'asc')
        ->orderBy('transactions.number', 'asc')
        ->get();
    }

    public function accReceivablesQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        $transactions = Transaction::MySales()
        ->join('taxpayers', 'taxpayers.id', 'transactions.customer_id')
        ->join('currencies', 'transactions.currency_id','currencies.id')
        ->join('transaction_details as td', 'td.transaction_id', 'transactions.id')
        ->leftJoin('account_movements', 'transactions.id', 'account_movements.transaction_id')
        ->where('transactions.supplier_id', $taxPayer->id)
        ->where('transactions.payment_condition', '>', 0)
        ->whereBetween('transactions.date', [$startDate, $endDate])
        ->groupBy('transactions.id')
        ->select(DB::raw('max(transactions.id) as ID'),
        DB::raw('max(taxpayers.name) as Customer'),
        DB::raw('max(taxpayers.taxid) as CutomerTaxID'),
        DB::raw('max(currencies.code) as Currency'),
        DB::raw('max(transactions.payment_condition) as PaymentCondition'),
        DB::raw('max(transactions.date) as Date'),
        DB::raw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY) as Expiry'),
        DB::raw('max(transactions.number) as Number'),
        DB::raw('ifnull(sum(account_movements.credit / account_movements.rate), 0) as Paid'),
        DB::raw('sum(td.value/transactions.rate) as Value'))
        ->orderByRaw('DATE_ADD(max(transactions.date), INTERVAL max(transactions.payment_condition) DAY)', 'desc')
        ->orderByRaw('max(transactions.number)', 'desc')
        ->get();
    }

    public function vatCreditNoteQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return TransactionDetail::join('charts', 'charts.id', 'transaction_details.chart_id')
        ->join('charts as vats', 'vats.id', 'transaction_details.chart_vat_id')
        ->join('transactions', 'transactions.id', 'transaction_details.transaction_id')
        ->join('taxpayers as supplier', 'transactions.supplier_id', 'supplier.id')
        ->join('taxpayers as customer', 'transactions.customer_id', 'customer.id')
        ->where('supplier.id', $taxPayer->id)
        ->where('transactions.type', 5)
        ->where('transactions.deleted_at', '=', null)
        ->whereBetween('transactions.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->select('customer.name as customer',
        'customer.taxid as customer_code',
        'transactions.type',
        'transactions.id as ID',
        'transactions.date',
        'transactions.code',
        'transactions.number',
        'transactions.payment_condition',
        'transactions.comment',
        'transactions.rate',
        'charts.name as costCenter',
        'vats.name as vat',
        'vats.coefficient',
        DB::raw('transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value) as localCurrencyValue,
        (transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value)) / (vats.coefficient + 1) as vatValue')
        )
        ->orderBy('transactions.date', 'asc')
        ->orderBy('transactions.number', 'asc')
        ->get();
    }

    public function vatDebitNoteQuery(Taxpayer $taxPayer, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return TransactionDetail::join('charts', 'charts.id', 'transaction_details.chart_id')
        ->join('charts as vats', 'vats.id', 'transaction_details.chart_vat_id')
        ->join('transactions', 'transactions.id', 'transaction_details.transaction_id')
        ->join('taxpayers as supplier', 'transactions.supplier_id', 'supplier.id')
        ->join('taxpayers as customer', 'transactions.customer_id', 'customer.id')
        ->where('customer.id', $taxPayer->id)
        ->where('transactions.type', 3)
        ->where('transactions.deleted_at', '=', null)
        ->whereBetween('transactions.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->select('customer.name as customer',
        'customer.taxid as customer_code',
        'transactions.type',
        'transactions.id as ID',
        'transactions.date',
        'transactions.code',
        'transactions.number',
        'transactions.payment_condition',
        'transactions.comment',
        'transactions.rate',
        'charts.name as costCenter',
        'vats.name as vat',
        'vats.coefficient',
        DB::raw('transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value) as localCurrencyValue,
        (transactions.rate * if(transactions.type = 5, -transaction_details.value, transaction_details.value)) / (vats.coefficient + 1) as vatValue')
        )
        ->orderBy('transactions.date', 'asc')
        ->orderBy('transactions.number', 'asc')
        ->get();
    }

    public function journalQuery(Taxpayer $taxPayer, $cycleID, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return Journal::join('journal_details', 'journals.id', 'journal_details.journal_id')
        ->join('charts', 'charts.id', 'journal_details.chart_id')
        ->select('journals.id',
        'journals.date',
        'journals.comment',
        'journals.number',
        'journal_details.debit',
        'journal_details.credit',
        'charts.id as chart_id',
        'charts.name as chartName',
        'charts.code as chartCode',
        'charts.type as chartType',
        'charts.sub_type as chartSubType')
        ->whereBetween('journals.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->where('cycle_id', $cycleID)
        ->get();
    }

    public function journalSummarizedQuery(Taxpayer $taxPayer, $cycleID, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return Journal::join('journal_details', 'journals.id', 'journal_details.journal_id')
        ->join('charts', 'charts.id', 'journal_details.chart_id')
        ->select(DB::raw('max(journals.id)',
        'max(journals.date)',
        'max(journals.comment)',
        'max(journals.number)',
        'sum(journal_details.debit)',
        'sum(journal_details.credit)',
        'max(charts.id) as chart_id',
        'max(charts.name) as chartName',
        'max(charts.code) as chartCode',
        'max(charts.type) as chartType',
        'max(charts.sub_type) as chartSubType'))
        ->whereBetween('journals.date', array(Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()))
        ->where('cycle_id', $cycleID)
        ->groupBy('journal_details.chart_id', 'journals.date')
        ->orderByRaw('max(journals.date)')
        ->get();
    }

    public function chartQuery(Taxpayer $taxPayer, $cycle, $startDate, $endDate)
    {
        DB::connection()->disableQueryLog();

        return Chart::orderBy('parent_id','asc')
        ->select('id', 'parent_id', 'code', 'name', 'type', 'sub_type', 'is_accountable')
        ->get();
    }
}
