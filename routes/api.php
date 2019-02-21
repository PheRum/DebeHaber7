<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
*/

// Route::group([ 'middleware' => 'auth:api' ], function () {

Route::prefix('{country}')->group(function ()
{
    Route::get('/get_owner/{taxPayerID}', 'TaxpayerController@get_owner');
    Route::get('/get_taxpayers/{searchBy}', 'TaxpayerController@get_taxpayer');
});

//Used for accepting or rejecting a team from accesing your taxpayer's data.
Route::prefix('teams')->group(function ()
{
    Route::post('/accpet-invite/{id}/{type}', 'HomeController@teamAccept')->name('team.accept');
    Route::post('/reject-invite/{id}', 'HomeController@teamReject')->name('team.reject');
});

Route::prefix('{taxPayer}')->group(function ()
{
    // This creates taxpayers to be used only in Sales and Purchases.
    // Not Taxpayers that will be used for accounting.
    Route::post('store-taxpayer', 'TaxpayerController@createTaxPayer');
    Route::get('get-rates/by/{currencyID}/{date?}', 'CurrencyRateController@get_ratesByCurrency');
    Route::get('documents/by/{type}', 'DocumentController@get_document');

    Route::prefix('config')->group(function ()
    {
        Route::resources([
            'chart-versions' => 'ChartVersionController',
            'cycles' => 'CycleController',
            'currencies' => 'CurrencyController',
            'rates' => 'CurrencyRateController',
            'documents' => 'DocumentController',
        ]);
    });

    Route::prefix('{cycle}')->group(function ()
    {
        Route::prefix('search')->group(function ()
        {
            Route::get('transactions/{q}', 'SearchController@searchTransactions');
            Route::get('taxpayers/{q}', 'SearchController@searchTaxPayers');
            Route::get('charts/{q}', 'SearchController@searchCharts');
        });

        Route::prefix('commercial')->group(function ()
        {
            Route::resources([
                'sales' => 'SalesController',
                'credit-notes' => 'CreditNoteController',
                'account-receivables' => 'AccountReceivableController',

                'purchases' => 'PurchaseController',
                'debit-notes' => 'DebitNoteController',
                'account-payables' => 'AccountPayableController',

                'money-movements' => 'AccountMovementController',
                'inventories' => 'InventoryController',
                'fixed-assets' => 'FixedAssetController',
            ]);

            // Route::get('sales/by-id/{id}', 'SalesController@get_salesByID');
            Route::get('sales/default/{partnerID}', 'SalesController@getLastSale');
            Route::get('sales/last', 'SalesController@get_lastDate');
            Route::get('purchases/default/{partnerID}', 'PurchaseController@getLastPurchase');

            Route::post('inventories/get_InventoryChartType', 'InventoryController@get_InventoryChartType');
            Route::post('inventories/calc-revenue', 'InventoryController@Calulate_sales');
            Route::post('inventories/calc-inventory', 'InventoryController@Calulate_InvenotryValue');
        });

        Route::prefix('accounting')->group(function ()
        {
            //maybe add global scope.
            Route::get('journal/ByCycleID/{id}', 'JournalController@getJournalsByCycleID');
            Route::get('journals/balance-sheet', 'AccountingController@getBalanceSheet');

            Route::resources([
                'journals' => 'JournalController',
                'budgets' => 'BudgetController',
                'opening-balance' => 'OpeningBalanceController',
                'closing-balance' => 'ClosingBalanceController',
                'charts' => 'ChartController',
            ]);

            Route::prefix('charts')->group(function ()
            {
                Route::prefix('for')->group(function ()
                {
                    Route::get('fixed-assets', 'ChartController@getFixedAssets');
                    Route::get('money', 'ChartController@getMoneyAccounts');

                    Route::get('income', 'ChartController@getSalesAccounts');
                    Route::get('expense', 'ChartController@getPurchaseAccounts');

                    Route::get('vats-credit', 'ChartController@getVATCredit');
                    Route::get('vats-debit', 'ChartController@getVATDebit');

                    Route::get('non-accountables', 'ChartController@getNonAccountableCharts');
                    Route::get('accountables', 'ChartController@getAccountableCharts');

                    // ?? TODO ?? What is this ??? frase???
                    Route::get('parent_accounts/{frase}', 'ChartController@getParentAccount');
                });

                Route::post('merge/{fromChartId}/{toChartId}', 'ChartController@mergeCharts');
                Route::post('merge-check/{fromChartId}', 'ChartController@checkMergeCharts');
            });
        });
    });

    Route::prefix('PRY')->group(function()
    {
        Route::get('/hechauka/{startDate}/{endDate}', 'API\PRY\HechukaController@getHechauka');
    });
});
