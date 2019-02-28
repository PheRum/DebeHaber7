<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TaxpayerIntegration;
use Auth;

class HomeController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('teamSubscribed');
        $this->middleware('verified');
    }

    /**
    * Show the application dashboard.
    *
    * @return Response
    */
    public function show()
    {
        $user = Auth()->user();

        if (isset($user) == false)
        {
            return view('welcome');
        }

        $taxPayerIntegrations = TaxpayerIntegration::MyTaxPayers($user->current_team->id)
        ->whereIn('status', [1, 2])
        ->with('taxpayer')
        ->paginate(10);

        $integrationInvites = TaxpayerIntegration::
        where('is_owner', 0)
        ->where('status', 1)
        ->whereIn('taxpayer_id', $taxPayerIntegrations->where('is_owner', '=', 1)->pluck('taxpayer_id'))
        ->with(['taxpayer', 'team'])
        ->get();

        return view('home')
        ->with('taxPayerIntegrations', $taxPayerIntegrations)
        ->with('integrationInvites', $integrationInvites);

        return view('home');
    }

    public function myTaxpayers()
    {
        $user = Auth()->user();

        if (isset($user) == false)
        {
            return view('welcome');
        }

        $taxPayerIntegrations = TaxpayerIntegration::MyTaxPayers($user->current_team->id)
        ->whereIn('status', [1, 2])
        ->with('taxpayer')
        ->with('taxpayer.setting')
        ->orderBy('name')
        ->paginate(10);

        return $taxPayerIntegrations;
    }
}
