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

Route::group([ 'middleware' => 'auth:api' ], function () {

    //Used for accepting or rejecting a team from accesing your taxpayer's data.
    Route::prefix('teams')->group(function ()
    {
        Route::post('/accpet-invite/{id}/{type}', 'HomeController@teamAccept')->name('team.accept');
        Route::post('/reject-invite/{id}', 'HomeController@teamReject')->name('team.reject');
    });

    Route::prefix('{taxPayer}')->group(function ()
    {
        // This creates taxpayers to be used only in Sales and Purchases. Not Taxpayers that will be used for accounting.
        Route::post('store-taxpayer', 'TaxpayerController@createTaxPayer');

        Route::get('get_cycle', 'CycleController@get_cycle');
        Route::get('get_chartversion', 'ChartVersionController@get_chartversion');
        Route::get('get_currency', 'CurrencyController@get_currency');
        Route::get('get_rates/{currencyID}/{date?}', 'CurrencyRateController@get_ratesByCurrency');

        Route::get('get_documents/{type}', 'DocumentController@get_document');
        Route::get('get_allDocuments', 'DocumentController@get_Alldocument');
        Route::get('get_documentByID/{id}', 'DocumentController@get_documentByID');

        Route::prefix('{cycle}')->group(function ()
        {
            Route::prefix('search')->group(function ()
            {
                Route::get('purchases/{q}', 'SearchController@searchPurchases');
                Route::get('debits/{q}', 'SearchController@searchDebits');
                Route::get('sales/{q}', 'SearchController@searchSales');
                Route::get('credits/{q}', 'SearchController@searchCredits');
                Route::get('taxpayers/{q}', 'SearchController@searchTaxPayers');
            });

            Route::prefix('accounting')->group(function ()
            {
                Route::get('journals', 'JournalController@getJournals');
                Route::get('journal/by-id/{id}', 'JournalController@getJournalsByID');
                Route::get('delete_journalByID/{id}', 'JournalController@getJournalsByID');
                Route::get('journal/ByCycleID/{id}', 'JournalController@getJournalsByCycleID');

                Route::get('opening_balance', 'OpeningBalanceController@index');
                Route::post('opening_balance', 'OpeningBalanceController@store');
                Route::delete('opening_balance', 'OpeningBalanceController@destroy');

                Route::get('budget', 'BudgetController@index');
                Route::post('budget', 'BudgetController@store');

                Route::get('closing_balance', 'ClosingBalanceController@index');
                Route::post('closing_balance', 'ClosingBalanceController@store');

                Route::prefix('chart')->group(function ()
                {
                    Route::get('charts', 'ChartController@getCharts');
                    Route::get('charts/by-id/{id}', 'ChartController@getChartsByID');
                    Route::get('get-fixedasset', 'ChartController@getFixedAssets');
                    Route::get('get-fixed-assets/{frase}', 'ChartController@searchFixedAssetsCharts');
                    Route::get('get-accountables', 'ChartController@getAccountableCharts');
                    Route::get('get-accountables/{frase}', 'ChartController@searchAccountableCharts');
                    Route::get('get-money_accounts', 'ChartController@getMoneyAccounts');
                    Route::get('get-parent_accounts/{frase}', 'ChartController@getParentAccount');
                    Route::post('merge/{fromChartId}/{toChartId}', 'ChartController@mergeCharts');
                    Route::post('merge-check/{fromChartId}', 'ChartController@checkMergeCharts');
                });

                Route::prefix('journals')->group(function ()
                {
                    Route::get('balance-sheet', 'AccountingController@getBalanceSheet');
                    Route::get('list', 'JournalController@getJournals');
                    Route::get('by-id/{id}', 'JournalController@getJournalsByID');
                });
            });

            Route::prefix('commercial')->group(function ()
            {
                Route::get('sales', 'SalesController@get_sales');
                Route::get('sales/by-id/{id}', 'SalesController@get_salesByID');
                Route::get('sales/default/{partnerID}', 'SalesController@getLastSale');
                Route::get('sales/last', 'SalesController@get_lastDate');
                Route::get('sales/get-charts', 'ChartController@getSalesAccounts');
                Route::get('sales/get-vats', 'ChartController@getVATDebit');

                Route::get('purchases', 'PurchaseController@get_purchases');
                Route::get('purchases/by-id/{id}', 'PurchaseController@get_purchasesByID');
                Route::get('purchases/default/{partnerID}', 'PurchaseController@getLastPurchase');
                Route::get('purchases/get-charts', 'ChartController@getPurchaseAccounts');
                Route::get('purchases/get-vats', 'ChartController@getVATCredit');

                Route::get('credit-notes', 'CreditNoteController@get_credit_note');
                Route::get('credit-notes/by-id/{id}', 'CreditNoteController@get_credit_noteByID');
                Route::get('credit-notes/get-charts', 'ChartController@getSalesAccounts');
                Route::get('credit-notes/get-vats', 'ChartController@getVATCredit');

                Route::get('debit-notes', 'DebitNoteController@get_debit_note');
                Route::get('debit-notes/by-id/{id}', 'DebitNoteController@get_debit_noteByID');
                Route::get('debit-notes/get-charts', 'ChartController@getPurchaseAccounts');
                Route::get('debit-notes/get-vats', 'ChartController@getVATDebit');

                Route::get('account_receivables', 'AccountReceivableController@get_account_receivable');
                Route::get('account_receivables/by-id/{id}', 'AccountReceivableController@get_account_receivableByID');
                Route::get('account_receivables/get-charts', 'ChartController@getMoneyAccounts');

                Route::get('account_payables', 'AccountPayableController@get_account_payable');
                Route::get('account_payables/by-id/{id}', 'AccountPayableController@get_account_payableByID');
                Route::get('account_payables/get-charts', 'ChartController@getMoneyAccounts');

                Route::get('money_movements', 'AccountMovementController@GetMovement');
                Route::get('money_movements/by-id/{id}', 'MoneyTransferController@get_money_transferByID');

                Route::get('inventories/{skip}', 'InventoryController@getInventories');
                Route::post('inventories/get_InventoryChartType', 'InventoryController@get_InventoryChartType');
                Route::post('inventories/calc-revenue', 'InventoryController@Calulate_sales');
                Route::post('inventories/calc-inventory', 'InventoryController@Calulate_InvenotryValue');

                Route::get('fixed-assets', 'FixedAssetController@getFixedAsset');
                Route::get('fixed-assets/by-id/{id}', 'FixedAssetController@getFixedAssetByID');
            });
        });


        Route::prefix('PRY')->group(function()
        {
            Route::get('/hechauka/{startDate}/{endDate}', 'API\PRY\HechukaController@getHechauka');
        });
    });
});
