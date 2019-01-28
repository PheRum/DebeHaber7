<?php

namespace App\Http\Controllers;

use App\Taxpayer;
use App\Chart;
use App\ChartAlias;
use App\Cycle;
use App\CycleBudget;
use App\FixedAsset;
use App\Inventory;
use App\Transaction;
use App\TransactionDetail;
use App\AccountMovement;
use App\JournalDetail;
use App\ProductionDetail;
use App\Enums\ChartTypeEnum;
use Illuminate\Http\Request;

class ChartController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Taxpayer $taxPayer, Cycle $cycle)
  {
    return GeneralResource::collection(Chart::orderBy('code')
    ->paginate(50));
  }



  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request, Taxpayer $taxPayer, Cycle $cycle)
  {
    $chart = Chart::firstOrNew('id', $request->id);

    $chart->chart_version_id = $cycle->chart_version_id;
    $chart->country = $taxPayer->country;
    $chart->taxpayer_id = $taxPayer->id;

    if ($request->parent_id > 0) {
      $chart->parent_id = $request->parent_id;
    }

    if ($request->is_accountable == true) {
      $chart->is_accountable = 1;
      $chart->sub_type = $request->sub_type;
    } else {
      $chart->is_accountable = 0;
      $chart->sub_type = 0;
    }

    if ($request->type > 0) {
      $chart->type = $request->type;
    }

    if ($request->coefficient > 0) {
      $chart->coefficient = $request->coefficient;
    }

    if ($request->asset_years > 0) {
      $chart->asset_years = $request->asset_years;
    }

    $chart->code = $request->code;
    $chart->name = $request->name;

    if ($request->partner_id > 0) {
      $chart->partner_id = $request->partner_id;
    }

    $chart->save();

    return response()->json(200);
  }



  /**
  * Show the form for editing the specified resource.
  *
  * @param  \App\Chart  $chart
  * @return \Illuminate\Http\Response
  */
  public function edit(Chart $chart)
  {
    return GeneralResource::collection(Chart::where('id',$chart->id)
    ->paginate(1));
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  \App\Chart  $chart
  * @return \Illuminate\Http\Response
  */
  public function destroy(Chart $chart)
  {
    try
      {
        Transaction::where('id', $transactionId)->delete();

        return response()->json('Ok', 200);
      }
      catch (\Exception $e)
      {
        return response()->json($e, 500);
      }
  }



  public function getAccountableCharts(Taxpayer $taxPayer, Cycle $cycle)
  {
    $charts = Chart::where('is_accountable', true)->orderBy('code')->get();
    return response()->json($charts);
  }

  public function getSalesAccounts(Taxpayer $taxPayer, Cycle $cycle)
  {
    $charts = Chart::SalesAccounts()
    ->orderBy('name')
    ->select('name', 'id', 'type')
    ->get();

    return response()->json($charts);
  }

  public function getFixedAssets(Taxpayer $taxPayer, Cycle $cycle)
  {
    $charts = Chart::FixedAssetGroups()
    ->orderBy('name')
    ->select('name', 'id', 'type')
    ->get();

    return response()->json($charts);
  }

  // Accounts used in Purchase. Expense + Fixed Assets
  public function getPurchaseAccounts(Taxpayer $taxPayer, Cycle $cycle)
  {
    $charts = Chart::PurchaseAccounts()
    ->orderBy('name')
    ->select('name', 'id', 'type')
    ->get();
    return response()->json($charts);
  }

  // Money Accounts
  public function getMoneyAccounts(Taxpayer $taxPayer, Cycle $cycle)
  {
    $charts = Chart::MoneyAccounts()->orderBy('name')
    ->select('name', 'id', 'sub_type')
    ->get();
    return response()->json($charts);
  }

  // Debit VAT, used in Sales. Also Normal Sales Tax (Not VAT).
  public function getVATDebit(Taxpayer $taxPayer, Cycle $cycle)
  {
    $charts = Chart::
    VATDebitAccounts()
    ->select('name', 'code', 'id', 'coefficient')
    ->get();

    return response()->json($charts);
  }

  // Credit VAT, used in Purchases
  public function getVATCredit(Taxpayer $taxPayer, Cycle $cycle)
  {
    $charts = Chart::
    VATCreditAccounts()
    ->select('name', 'code', 'id', 'coefficient')
    ->get();
    return response()->json($charts);
  }

  // Improve with Elastic Search
  public function getParentAccount(Taxpayer $taxPayer, Cycle $cycle, $query)
  {
    $charts = Chart::where('is_accountable', false)
    ->where(function ($q) use ($query)
    {
      $q->where('name', 'like', '%' . $query . '%')
      ->orWhere('code', 'like', '%' . $query . '%')
      ->orWhereHas('aliases', function($subQ) use($query) {
        $subQ->where('name', 'like', '%' . $query . '%');
      });
    })
    ->with('children:name')
    ->get();

    return response()->json($charts);
  }

  public function searchAccountableCharts(Taxpayer $taxPayer, Cycle $cycle, $query)
  {
    $charts = Chart::where('is_accountable', true)
    ->where(function ($q) use ($query)
    {
      $q->where('name', 'like', '%' . $query . '%')
      ->orWhere('code', 'like', '%' . $query . '%')
      ->orWhereHas('aliases', function($subQ) use($query) {
        $subQ->where('name', 'like', '%' . $query . '%');
      });
    })
    ->with('children:name')
    ->get();

    return response()->json($charts);
  }

  public function searchFixedAssetsCharts(Taxpayer $taxPayer, Cycle $cycle, $query)
  {
    $charts = Chart::FixedAssetGroups()
    ->where(function ($q) use ($query)
    {
      $q->where('name', 'like', '%' . $query . '%')
      ->orWhere('code', 'like', '%' . $query . '%')
      ->orWhereHas('aliases', function($subQ) use($query) {
        $subQ->where('name', 'like', '%' . $query . '%');
      });
    })
    ->with('children:name')
    ->get();

    return response()->json($charts);
  }

  public function createIfNotExists_FixedAsset(Taxpayer $taxPayer, Cycle $cycle, $assetGroup, $lifeSpan)
  {
    $chart = Chart::My($taxPayer, $cycle)
    ->where('type', 1)
    ->where('sub_type', 9)
    ->where('is_accountable', true)
    ->where('name', $assetGroup)
    ->where('asset_years', $lifeSpan)
    ->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 1;
      $chart->sub_type = 9;
      $chart->is_accountable = true;
      $chart->name = $assetGroup ?? __('enum.FixedAssets');
      $chart->asset_years = $lifeSpan;
      $chart->code = 'N/A';
      $chart->save();
    }

    return $chart;
  }

  public function createIfNotExists_Inventory(Taxpayer $taxPayer, Cycle $cycle, $chartName = '')
  {
    $query = Chart::My($taxPayer, $cycle)
    ->where('type', 1)
    ->where('sub_type', 8)
    ->where('is_accountable', true);

    if ($chartName != '') {
      $query->where(function ($q) use ($chartName){
        $q->where('name', $chartName)
        ->orWhereHas('aliases', function($subQ) use($chartName) {
          $subQ->where('name', 'like', '%' . $chartName . '%');
        });
      });
    }

    $chart = $query->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 1;
      $chart->sub_type = 8;
      $chart->is_accountable = true;
      $chart->name = $chartName != '' ? $chartName : __('enum.Inventory');
      $chart->code = 'N/A';
      $chart->save();
    }

    return $chart;
  }

  public function createIfNotExists_CashAccounts(Taxpayer $taxPayer, Cycle $cycle, $chartName = '')
  {
    $query = Chart::My($taxPayer, $cycle)
    ->where('type', 1)
    ->where(function($subQ) use ($taxPayer, $cycle)
    {
      $subQ->where('sub_type', 1)
      ->orWhere('sub_type', 3);
    })
    ->where('is_accountable', true);

    if ($chartName != '') {
      $query->where(function ($q) use ($chartName){
        $q->where('name', $chartName)
        ->orWhereHas('aliases', function($subQ) use($chartName) {
          $subQ->where('name', 'like', '%' . $chartName . '%');
        });
      });
    }

    $chart = $query->first();

    if (!isset($chart))
    {
      //if not, create generic.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 1;
      $chart->sub_type = 1;
      $chart->is_accountable = true;
      $chart->code = 'N/A';
      $chart->name = $chartName != '' ? $chartName : __('enum.PettyCash');
      $chart->save();
    }

    return $chart;
  }

  public function createIfNotExists_AccountsReceivables(Taxpayer $taxPayer, Cycle $cycle, $partnerID)
  {
    //Check if CustomerID exists in Chart.
    $chart = Chart::My($taxPayer, $cycle)
    ->where('type', 1)
    ->where('sub_type', 5)
    ->where('partner_id', $partnerID)
    ->first();

    if (!isset($chart))
    {
      //if not, then look for generic.
      $chart = Chart::My($taxPayer, $cycle)
      ->where('type', 1)
      ->where('sub_type', 5)
      ->where('is_accountable', true)
      ->whereNull('partner_id')
      ->first();

      if (!isset($chart))
      {
        //if not, create specific.
        $chart = new Chart();
        $chart->taxpayer_id = $taxPayer->id;
        $chart->chart_version_id = $cycle->chart_version_id;
        $chart->partner_id = $partnerID;
        $chart->type = 1;
        $chart->sub_type = 5;
        $chart->is_accountable = true;
        $chart->code = 'N/A';
        $chart->name = __('commercial.AccountsReceivable') . ' ' . Taxpayer::find($partnerID)->name;
        $chart->save();
      }
    }

    return $chart;
  }

  public function createIfNotExists_AccountsPayable(Taxpayer $taxPayer, Cycle $cycle, $partnerID)
  {
    //Check if CustomerID exists in Chart.
    $chart = Chart::My($taxPayer, $cycle)
    ->where('type', 2)
    ->where('sub_type', 1)
    ->where('partner_id', $partnerID)
    ->first();

    if (!isset($chart))
    {
      //if not, then look for generic.
      $chart = Chart::My($taxPayer, $cycle)
      ->where('type', 2)
      ->where('sub_type', 1)
      ->where('is_accountable', true)
      ->whereNull('partner_id')
      ->first();

      if (!isset($chart))
      {
        //if not, create specific.
        $chart = new Chart();
        $chart->taxpayer_id = $taxPayer->id;
        $chart->chart_version_id = $cycle->chart_version_id;
        $chart->partner_id = $partnerID;
        $chart->type = 2;
        $chart->sub_type = 1;
        $chart->is_accountable = true;
        $chart->code = 'N/A';
        $chart->name = __('commercial.AccountsPayable') . ' ' . Taxpayer::find($partnerID)->name;
        $chart->save();
      }
    }

    return $chart;
  }

  // Income on General Services
  public function createIfNotExists_Incomes(Taxpayer $taxPayer, Cycle $cycle, $chartName = '')
  {
    $query = Chart::My($taxPayer, $cycle)
    ->where('type', 4)
    ->where('sub_type', 1)
    ->where('is_accountable', true);

    if ($chartName != '') {
      $query->where(function ($q) use ($chartName){
        $q->where('name', $chartName)
        ->orWhereHas('aliases', function($subQ) use($chartName) {
          $subQ->where('name', 'like', '%' . $chartName . '%');
        });
      });
    }

    $chart = $query->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 4;
      $chart->sub_type = 1;
      $chart->is_accountable = true;
      $chart->code = 'N/A';
      $chart->name = $chartName != '' ? $chartName : __('enum.Revenue');
      $chart->save();
    }

    return $chart;
  }

  // Income on Inventory Products
  public function createIfNotExists_IncomeFromInventory(Taxpayer $taxPayer, Cycle $cycle, $chartName = '')
  {
    $query = Chart::My($taxPayer, $cycle)
    ->where('type', 4)
    ->where('sub_type', 4)
    ->where('is_accountable', true);

    if ($chartName != '') {
      $query->where(function ($q) use ($chartName){
        $q->where('name', $chartName)
        ->orWhereHas('aliases', function($subQ) use($chartName) {
          $subQ->where('name', 'like', '%' . $chartName . '%');
        });
      });
    }

    $chart = $query->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 4;
      $chart->sub_type = 4;
      $chart->is_accountable = true;
      $chart->code = 'N/A';
      $chart->name = $chartName != '' ? $chartName : __('enum.RevenuFromInventory');
      $chart->save();
    }

    return $chart;
  }

  // Income from Foreign Exchange
  public function createIfNotExists_IncomeFromFX(Taxpayer $taxPayer, Cycle $cycle)
  {
    $chart = Chart::My($taxPayer, $cycle)
    ->where('type', 4)
    ->where('sub_type', 3)
    ->where('is_accountable', true)
    ->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 4;
      $chart->sub_type = 3;
      $chart->is_accountable = true;
      $chart->code = 'N/A';
      $chart->name = __('enum.DiffInExchangeRate');
      $chart->save();
    }

    return $chart;
  }

  // Expense on General Services. Expenses on Products are on Fixed Assets and not Expense.
  public function createIfNotExists_Expenses(Taxpayer $taxPayer, Cycle $cycle, $chartName = '')
  {
    $query = Chart::My($taxPayer, $cycle)
    ->where('type', 5)
    ->where('sub_type', 10)
    ->where('is_accountable', true);

    if ($chartName != '') {
      $query->where(function ($q) use ($chartName){
        $q->where('name', $chartName)
        ->orWhereHas('aliases', function($subQ) use($chartName) {
          $subQ->where('name', 'like', '%' . $chartName . '%');
        });
      });
    }

    $chart = $query->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 5;
      $chart->sub_type = 10;
      $chart->is_accountable = true;
      $chart->code = 'N/A';
      $chart->name = $chartName != '' ? $chartName : __('enum.OtherExpenses');
      $chart->save();
    }

    return $chart;
  }

  // Expense from Foreign Exchange
  public function createIfNotExists_ExpenseFromFX(Taxpayer $taxPayer, Cycle $cycle)
  {
    $chart = Chart::My($taxPayer, $cycle)
    ->where('type', 5)
    ->where('sub_type', 11)
    ->where('is_accountable', true)
    ->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 5;
      $chart->sub_type = 11;
      $chart->is_accountable = true;
      $chart->code = 'N/A';
      $chart->name = __('enum.DiffInExchangeRate');
      $chart->save();
    }

    return $chart;
  }

  public function createIfNotExists_VATWithholdingReceivables(Taxpayer $taxPayer, Cycle $cycle)
  {
    $chart = Chart::My($taxPayer, $cycle)
    ->where('type', 1)
    ->where('sub_type', 13)
    ->where('is_accountable', true)
    ->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 1;
      $chart->sub_type = 13;
      $chart->is_accountable = true;
      $chart->code = 'N/A';
      $chart->name = __('enum.VatWithholdings');
      $chart->save();
    }

    return $chart;
  }

  public function createIfNotExists_VATWithholdingPayables(Taxpayer $taxPayer, Cycle $cycle)
  {
    $chart = Chart::My($taxPayer, $cycle)
    ->where('type', 2)
    ->where('sub_type', 7)
    ->where('is_accountable', true)
    ->first();

    if (!isset($chart))
    {
      //if not, create specific.
      $chart = new Chart();
      $chart->taxpayer_id = $taxPayer->id;
      $chart->chart_version_id = $cycle->chart_version_id;
      $chart->type = 2;
      $chart->sub_type = 7;
      $chart->is_accountable = true;
      $chart->code = 'N/A';
      $chart->name = __('enum.VatWithholdings');
      $chart->save();
    }

    return $chart;
  }

  public function checkMergeCharts(Taxpayer $taxPayer, Cycle $cycle, $fromChartId)
  {
    //run validation on chart types and make sure a transfer can take place.
    $fromChart = Chart::My($taxPayer, $cycle)->where('id', $fromChartId)->first();

    if (isset($fromChart))
    {
      $count = 0;

      $count += CycleBudget::where('chart_id', $fromChartId)->count();
      $count += ProductionDetail::where('chart_id', $fromChartId)->count();
      $count += Inventory::where('chart_id', $fromChartId)->count();
      $count += Chart::where('parent_id', $fromChartId)->count();
      $count += FixedAsset::where('chart_id', $fromChartId)->count();
      $count += Transaction::where('chart_account_id', $fromChartId)->count();
      $count += TransactionDetail::where('chart_id', $fromChartId)->count();
      $count += TransactionDetail::where('chart_vat_id', $fromChartId)->count();
      $count += AccountMovement::where('chart_id', $fromChartId)->count();
      $count += JournalDetail::where('chart_id', $fromChartId)->count();

      if ($count > 0)
      {
        return response()->json('Unable to Delete. Total of ' . $count . ' relationships exists, try Merge.', 500);
      }
      else
      {
        $fromChart->forceDelete();
        return response()->json('Ok', 200);
      }
    }

    return response()->json('Chart not found', 404);
  }

  public function mergeCharts(Taxpayer $taxPayer, Cycle $cycle, $fromChartId, $toChartId)
  {
    //run validation on chart types and make sure a transfer can take place.
    $fromChart = Chart::My($taxPayer, $cycle)->where('id', $fromChartId);
    $toChart = Chart::My($taxPayer, $cycle)->where('id', $toChartId);

    if (isset($fromChart) && isset($toChart))
    {
      CycleBudget::where('chart_id', $fromChartId)->update(['chart_id' => $toChartId]);
      FixedAsset::where('chart_id', $fromChartId)->update(['chart_id' => $toChartId]);
      ProductionDetail::where('chart_id', $fromChartId)->update(['chart_id' => $toChartId]);

      Inventory::where('chart_id', $fromChartId)->update(['chart_id' => $toChartId]);
      //update all transaction money accounts
      Transaction::where('chart_account_id', $fromChartId)->update(['chart_account_id' => $toChartId]);
      //update all transaction details and vats
      TransactionDetail::where('chart_id', $fromChartId)->update(['chart_id' => $toChartId]);
      TransactionDetail::where('chart_vat_id', $fromChartId)->update(['chart_vat_id' => $toChartId]);
      //update all account movements
      AccountMovement::where('chart_id', $fromChartId)->update(['chart_id' => $toChartId]);
      //update all journal details
      JournalDetail::where('chart_id', $fromChartId)->update(['chart_id' => $toChartId]);
      //Fix all parents
      Chart::where('parent_id', $fromChartId)->update(['parent_id' => $toChartId]);

      //add alias to new chart
      $alias = new ChartAlias();
      $alias->chart_id = $toChartId;
      $alias->name = $fromChart->first()->name;
      $alias->save();

      //delete $fromCharts
      $fromChart->forceDelete();

      return response()->json('Ok', 200);
    }

    return response()->json('Chart not found', 404);
  }

  public function organizeChartCode(Taxpayer $taxPayer, Cycle $cycle)
  {

  }
}
