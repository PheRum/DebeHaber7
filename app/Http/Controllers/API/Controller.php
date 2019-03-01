<?php

namespace App\Http\Controllers\API;

use App\Currency;
use App\CurrencyRate;
use App\Taxpayer;
use App\Chart;
use App\Cycle;
use App\ChartVersion;
use App\Http\Controllers\ChartController;
use DateTime;
use Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function checkCycle(Taxpayer $taxPayer, $firstDate) {


        $version = ChartVersion::where('country', $taxPayer->country)
        ->orWhere('taxpayer_id', $taxPayer->id)
        ->first();

        if (!isset($version))
        {
            $version = new ChartVersion();
            $version->taxpayer_id = $taxPayer->id;
            $version->name = 'Version Automatica';
            $version->save();
        }

        $cycle = new Cycle();
        $cycle->chart_version_id = $version->id;
        $cycle->year = $firstDate->year;
        $cycle->start_date = new Carbon('first day of January ' . $firstDate->year);
        $cycle->end_date = new Carbon('last day of December ' . $firstDate->year);
        $cycle->taxpayer_id = $taxPayer->id;
        $cycle->save();


        return $cycle;
    }

    public function checkServer(Request $request) {
        return response()->json('Ready to rock ... your accounting data', 200);
    }

    public function checkAPI(Request $request) {
        if (Auth::user() != null)
        { return response()->json(Auth::user()->name, 200); }
        else
        { return response()->json('Forbidden Access', 403); }
    }

    public function checkTaxPayer($taxID, $name) {
        $cleanTaxID = strtok($taxID , '-');
        $cleanDV = substr($taxID , -1);

        if (is_numeric($cleanTaxID))
        { $taxPayer = Taxpayer::where('taxid', $cleanTaxID)->first(); }
        else
        { $taxPayer = Taxpayer::where('taxid', '88888801')->first(); }

        if (!isset($taxPayer))
        {
            $taxPayer = new taxpayer();
            $taxPayer->name = $name ?? 'No Name';

            if ($cleanTaxID == false)
            {
                $taxPayer->taxid = 88888801;
                $taxPayer->code = null;
            }
            else
            {
                $taxPayer->taxid = $cleanTaxID ?? 88888801;
                $taxPayer->code = is_numeric($cleanDV) ? $cleanDV : null;
            }

            $taxPayer->alias = substr($name, 0, 29) . '...';;
            $taxPayer->save();
        }

        return $taxPayer;
    }

    public function checkCurrency($currencyCode, Taxpayer $taxPayer) {
        //Check if Chart Exists
        if ($currencyCode != '')
        {
            $currency = Currency::where('code', $currencyCode)
            ->where('country', $taxPayer->country)
            ->first();

            if ($currency == null)
            {
                $currency = new Currency();
                $currency->country = $taxPayer->country;
                $currency->code = $currencyCode;
                $currency->name = $currencyCode;
                $currency->save();
            }

            return $currency->id;
        }

        return null;
    }

    public function checkCurrencyRate($id, Taxpayer $taxPayer, $date) {
        $currencyRate = CurrencyRate::where('currency_id',$id)
        ->where('created_at', $this->convert_date($date))
        ->first();

        if (isset($currencyRate))
        { return $currencyRate->rate; }

        return null;
    }

    public function checkChart($costcenter, $name, Taxpayer $taxPayer, Cycle $cycle, $type) {
        $chartController= new ChartController();
        //Check if Chart Exists
        if (isset($costcenter))
        {
            $chart = null;
            //Type 1 = Service
            if ($costcenter == 1) {
                //Sales
                if ($type == 4 || $type == 5)
                {
                    $chart=$chartController->createIfNotExists_Incomes($taxPayer,$cycle,$costcenter->Name);
                }
                else //Purchase
                {
                    $chart=$chartController->createIfNotExists_Expenses($taxPayer,$cycle,$costcenter->Name??'');
                }
            }
            //Type 2 = Products
            elseif ($costcenter == 2) {
                //Sales
                if ($type == 4 || $type == 5)
                {
                    $chart = $chartController->createIfNotExists_IncomeFromInventory($taxPayer,$cycle,$costcenter->Name??'');
                }
                else //Purchase
                {
                    $chart = $chartController->createIfNotExists_Inventory($taxPayer,$cycle,$costcenter->Name??'');
                }
            }
            //Type 3 = FixedAsset
            elseif ($costcenter == 3) {
                $chart = Chart::My($taxPayer, $cycle)
                ->where('type', 1)
                ->where('sub_type', 9)
                ->where('is_accountable', true)
                ->where('name', $costcenter->Name)
                ->first();

                if (!isset($chart)) {
                    //if not, create specific.
                    $chart = new Chart();
                    $chart->taxpayer_id = $taxPayer->id;
                    $chart->chart_version_id = $cycle->chart_version_id;
                    $chart->type = 1;
                    $chart->sub_type = 9;
                    $chart->is_accountable = true;
                    $chart->name = $costcenter->Name ?? __('enum.FixedAssets');
                    $chart->code = 'N/A';
                    $chart->save();
                }
            }

            return $chart->id;
        }

        return null;
    }

    //These Charts will not work as they use the global scope for Taxpayer and Cycle.
    //you will have to call no global scopes for these methods and then manually assign the same query.
    public function checkChartOld($costcenter, $name, Taxpayer $taxPayer, Cycle $cycle, $type)
    {
        //Check if Chart Exists
        if (isset($costcenter))
        {
            $chart = null;
            //Type 1 = Service
            if ($costcenter == 1)
            {
                //Sales
                if ($type == 4 || $type == 5)
                {
                    $chart = Chart::My($taxPayer, $cycle)
                    ->Incomes()
                    ->where('name', $name)
                    ->orWhereHas('aliases', function($subQ) use($name) {
                        $subQ->where('name', 'like', '%' . $name . '%');
                    })
                    ->first();

                    if ($chart == null)
                    {
                        $chart = new Chart();
                        $chart->type = 4;
                        $chart->sub_type = 1;
                    }
                }
                else //Purchase
                {
                    $chart = Chart::My($taxPayer, $cycle)
                    ->Expenses()
                    ->where('name', $name)
                    ->orWhereHas('aliases', function($subQ) use($name) {
                        $subQ->where('name', 'like', '%' . $name . '%');
                    })
                    ->first();

                    if ($chart == null)
                    {
                        $chart = new Chart();
                        $chart->type = 5;
                        $chart->sub_type = 10;
                    }
                }
            }
            //Type 2 = Products
            elseif ($costcenter == 2)
            {
                //Sales
                if ($type == 4 || $type == 5)
                {
                    $chart = Chart::My($taxPayer, $cycle)
                    ->RevenuFromInventory()
                    ->where('name', $name)
                    ->orWhereHas('aliases', function($subQ) use($name) {
                        $subQ->where('name', 'like', '%' . $name . '%');
                    })
                    ->first();

                    if ($chart == null)
                    {
                        $chart = new Chart();
                        $chart->type = 4;
                        $chart->sub_type = 4;
                    }

                }
                else //Purchase
                {
                    $chart = Chart::My($taxPayer, $cycle)
                    ->Inventories()
                    ->where('name', $name)
                    ->orWhereHas('aliases', function($subQ) use($name) {
                        $subQ->where('name', 'like', '%' . $name . '%');
                    })
                    ->first();

                    if ($chart == null)
                    {
                        $chart = new Chart();
                        $chart->type = 1;
                        $chart->sub_type = 8;
                    }
                }
            }
            //Type 3 = FixedAsset
            elseif ($costcenter == 3)
            {
                $chart = Chart::My($taxPayer, $cycle)
                ->fixedAssets()
                ->where('name', $name)
                ->orWhereHas('aliases', function($subQ) use($name) {
                    $subQ->where('name', 'like', '%' . $name . '%');
                })
                ->first();

                if ($chart == null)
                {
                    $chart = new Chart();
                    $chart->type = 1;
                    $chart->sub_type = 9;
                }
            }

            //If chart is saved, then ignore this code.
            if ($chart->id == 0)
            {
                $chart->chart_version_id = $cycle->chart_version_id;
                $chart->country = $taxPayer->country;
                $chart->taxpayer_id = $taxPayer->id;
                $chart->is_accountable = true;
                $chart->code = 'N/A';
                $chart->name = $name;
                $chart->save();
            }

            return $chart->id;
        }

        return null;
    }

    public function checkDebitVAT($coefficient, Taxpayer $taxPayer, Cycle $cycle)
    {

        //Check if Chart Exists
        if ($coefficient != '' || $coefficient == 0)
        {

            $chart = Chart::My($taxPayer, $cycle)
            ->VATDebitAccounts()
            ->where('coefficient', $coefficient/100)
            ->first();

            if ($chart == null)
            {
                $chart = new Chart();
                $chart->chart_version_id = $cycle->chart_version_id;
                $chart->country = $taxPayer->country;
                $chart->taxpayer_id = $taxPayer->id;
                $chart->is_accountable = true;
                $chart->type = 2;
                $chart->sub_type = 3;
                $chart->coefficient = $coefficient / 100;

                $chart->code = 'N/A';

                $chart->name = 'Vat Debit ' . $coefficient;
                $chart->level = 1;

                $chart->save();
            }

            return $chart->id;
        }

        return null;
    }

    public function checkCreditVAT($coefficient, Taxpayer $taxPayer, Cycle $cycle)
    {
        //Check if Chart Exists
        if ($coefficient != '' || $coefficient == 0)
        {
            $chart = Chart::My($taxPayer, $cycle)
            ->VATCreditAccounts()
            ->where('coefficient', $coefficient/100)
            ->first();

            if ($chart == null)
            {
                $chart = new Chart();
                $chart->chart_version_id = $cycle->chart_version_id;
                $chart->country = $taxPayer->country;
                $chart->taxpayer_id = $taxPayer->id;
                $chart->is_accountable = true;
                $chart->type = 2;
                $chart->sub_type = 3;
                $chart->coefficient = $coefficient / 100;

                $chart->code = 'N/A';

                $chart->name = 'Vat Credit ' . $coefficient;
                $chart->level = 1;

                $chart->save();
            }

            return $chart->id;
        }

        return null;
    }

    public function checkChartAccount($name, Taxpayer $taxPayer, Cycle $cycle)
    {
        //Check if Chart Exists
        if ($name != '')
        {
            //TODO Wrong, you need to specify taxpayerID or else you risk bringing other accounts not belonging to taxpayer.
            //I have done this already.
            $chart = Chart::My($taxPayer, $cycle)
            ->MoneyAccounts()
            ->where('name', $name)
            ->orWhereHas('aliases', function($subQ) use($name) {
                $subQ->where('name', 'like', '%' . $name . '%');
            })
            ->first();

            if ($chart == null)
            {
                $chart = new Chart();
                $chart->chart_version_id = $cycle->chart_version_id;
                $chart->country = $taxPayer->country;
                $chart->taxpayer_id = $taxPayer->id;
                $chart->is_accountable = true;
                $chart->type = 1;
                $chart->sub_type = 3;

                $chart->code = 'N/A';
                $chart->name = $name;
                $chart->save();
            }

            return $chart->id;
        }

        return null;
    }

    public function convert_date($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date);
    }
}
