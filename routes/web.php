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

Route::get('/', 'WelcomeController@show');
Route::get('/home', 'HomeController@show');

Route::group(['middleware' => 'auth'], function ()
{
    Route::get('selectTaxPayer/{taxPayer}', 'TaxpayerController@selectTaxpayer')->name('selectTaxPayer');

    //Taxpayer Setting Routes
    Route::get('taxpayer/{id}', 'TaxpayerController@show')->name('editTaxPayer');
    Route::post('taxpayer', 'TaxpayerController@store')->name('postTaxPayer');
    Route::delete('taxpayer', 'TaxpayerController@destroy')->name('deleteTaxPayer');

    Route::prefix('{taxPayer}/{cycle}')->middleware('accessTaxPayer')->group(function ()
    {
        Route::get('', 'TaxpayerController@showDashboard')->name('taxpayer.dashboard');
        Route::get('{any}', function () { return view('platform'); })->where('any','.*');
    });
});
