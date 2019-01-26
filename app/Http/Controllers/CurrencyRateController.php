<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\TaxpayerSetting;
use App\Cycle;
use App\Currency;
use App\CurrencyRate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Swap\Laravel\Facades\Swap;
use DB;

class CurrencyRateController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Taxpayer $taxPayer, Cycle $cycle)
    {
        return view('/configs/currencies/list');
    }

    /**
    * Gets the rates for a specific currency by Date. If rate doesn't exist,
    * then it will search on the internet and create the rate for future use.
    *
    * @return \Illuminate\Http\Response
    */
    public function get_ratesByCurrency($taxPayerID, $currencyID, $date)
    {
        $date = Carbon::parse($date) ?? Carbon::now();

        $currencyRate = CurrencyRate::where('currency_id', $currencyID)
        ->whereDay('date', '=', $date->day)
        ->whereMonth('date', '=', $date->month)
        ->whereYear('date', '=', $date->year)
        ->where(function ($q) use($taxPayerID) {
            $q->whereNull('taxpayer_id')
            ->orWhere('taxpayer_id', $taxPayerID);
        })
        ->orderBy('taxpayer_id')
        ->first();

        if (isset($currencyRate))
        {
            return response()->json($currencyRate);
        }
        else
        {
            //swap fx
            $currCode = Currency::where('id', $currencyID)->select('code')->first()->code;
            $currCompanyCode = TaxpayerSetting::where('taxpayer_id', $taxPayerID)->select('currency')->first()->currency;

            //check that CompanyCode, CurrencyCode, and that both aren't the same.
            if ($currCompanyCode != null && $currCode != null && $currCompanyCode != $currCode)
            {
                //$str = 'USD/EUR';
                $str = $currCode . '/' . $currCompanyCode;
                $rate = Swap::historical($str, Carbon::parse($date));

                $currencyRate = new CurrencyRate();
                $currencyRate->date = $date;
                $currencyRate->currency_id = $currencyID;
                $currencyRate->buy_rate = $rate->getValue();
                $currencyRate->sell_rate = $rate->getValue();
                $currencyRate->save();

                return response()->json($currencyRate);
            }
        }

        //Create object, but do not store data.
        $currencyRate = new CurrencyRate();
        $currencyRate->date = $date;
        $currencyRate->currency_id = $currencyID;
        $currencyRate->buy_rate = 1;
        $currencyRate->sell_rate = 1;

        return response()->json($currencyRate);
    }

    public function get_Allrate()
    {
        $currencyRate = CurrencyRate::with('currency')
        ->get();

        return response()->json($currencyRate);
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
        $currencyrate = $request->id == 0 ? $currencyrate = new CurrencyRate() : CurrencyRate::where('id', $request->id)->first();

        $currencyrate->currency_id = $request->currency_id;
        $currencyrate->buy_rate = $request->buy_rate;
        $currencyrate->sell_rate = $request->sell_rate;
        $currencyrate->save();

        return response()->json('Ok', 200);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\CurrencyRate  $currencyRate
    * @return \Illuminate\Http\Response
    */
    public function show(CurrencyRate $currencyRate)
    {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\CurrencyRate  $currencyRate
    * @return \Illuminate\Http\Response
    */
    public function edit(CurrencyRate $currencyRate)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\CurrencyRate  $currencyRate
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, CurrencyRate $currencyRate)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\CurrencyRate  $currencyRate
    * @return \Illuminate\Http\Response
    */
    public function destroy(CurrencyRate $currencyRate)
    {
        //
    }
}
