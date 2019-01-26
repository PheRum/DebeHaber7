<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Cycle;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Resources\ModelResource;

class SearchController extends Controller
{
    public function search(Taxpayer $taxPayer, Cycle $cycle, $q)
    {
        $purchases = $this->searchPurchases($taxPayer, $cycle, $q);

        $debits = $this->searchDebits($taxPayer, $cycle, $q);

        $sales = $this->searchSales($taxPayer, $cycle, $q);

        $credits = $this->searchCredits($taxPayer, $cycle, $q);

        $taxPayers = $this->searchTaxPayers($taxPayer, $cycle, $q);

        return view('search')
        ->with('purchases', $purchases)
        ->with('debits', $debits)
        ->with('sales', $sales)
        ->with('credits', $credits)
        ->with('taxPayers', $taxPayers)
        ->with('q', $q);
    }

    public function searchPurchases(Taxpayer $taxPayer, Cycle $cycle, $q)
    {
        $results = Transaction::search($q)
        ->where('customer_id', $taxPayer->id)
        ->where('type', 2)
        ->paginate(25);

        return ModelResource::collection($results->load('supplier'));
    }

    public function searchDebits(Taxpayer $taxPayer, Cycle $cycle, $q)
    {
        $results = Transaction::search($q)
        ->where('customer_id', $taxPayer->id)
        ->where('type', 3)
        ->paginate(25);

        return ModelResource::collection($results->load('supplier'));
    }

    public function searchSales(Taxpayer $taxPayer, Cycle $cycle, $q)
    {
        $results = Transaction::search($q)
        ->where('supplier_id', $taxPayer->id)
        ->where('type', 4)
        ->paginate(25);

        return ModelResource::collection($results->load('customer'));
    }

    public function searchCredits(Taxpayer $taxPayer, Cycle $cycle, $q)
    {
        $results = Transaction::search($q)
        ->where('supplier_id', $taxPayer->id)
        ->where('type', 4)
        ->paginate(25);

        return ModelResource::collection($results->load('customer'));
    }

    public function searchTaxPayers(Taxpayer $taxPayer, Cycle $cycle, $q)
    {
        return Taxpayer::search($q)
        ->get();
    }
}
