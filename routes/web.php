<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//
Route::get('/', 'WelcomeController@show');
Route::get('/home', 'HomeController@show');

Route::group(['middleware' => 'auth'], function ()
{
    Route::get('selectTaxPayer/{taxPayer}', 'TaxpayerController@selectTaxpayer')->name('selectTaxPayer');

    Route::prefix('{taxPayer}/{cycle}')->group(function ()
    {
        Route::get('', 'TaxpayerController@showDashboard')->name('taxpayer.dashboard');
        Route::get('{any}', function () { return view('platform'); })->where('any','.*');
    });
});
