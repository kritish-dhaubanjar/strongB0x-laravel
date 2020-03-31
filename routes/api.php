<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/* Company */
Route::prefix('company')->group(function(){
    Route::get('/', 'Companies@index');
    Route::put('/', 'Companies@update');
});

/* Contact */
Route::prefix('contacts')->group(function(){
    Route::get('{type}', 'Contacts@index');
    Route::get('show/{id}', 'Contacts@show');
    Route::post('/', 'Contacts@store');
    Route::put('{id}', 'Contacts@update');
    Route::delete('{id}', 'Contacts@destroy');
});

/* Units */
Route::prefix('units')->group(function(){
    Route::get('/', 'Units@index');
    Route::post('/', 'Units@store');
    Route::put('{id}', 'Units@update');
    Route::delete('{id}', 'Units@destroy');
});

/* Accounts */
Route::prefix('accounts')->group(function(){
    Route::get('/', 'Accounts@index');
    Route::get('{id}', 'Accounts@show');
    Route::post('/', 'Accounts@store');
    Route::put('{id}', 'Accounts@update');
    Route::delete('{id}', 'Accounts@destroy');
});

/* Categories */
Route::prefix('categories')->group(function(){
    Route::get('/', 'Categories@index');
    Route::get('/income', 'Categories@income');
    Route::get('/expense', 'Categories@expense');
    Route::get('{id}', 'Categories@show');
    Route::post('/', 'Categories@store');
    Route::put('{id}', 'Categories@update');
    Route::delete('{id}', 'Categories@destroy');
});

/* Taxes */
Route::prefix('taxes')->group(function(){
    Route::get('/', 'Taxes@index');
    Route::get('{id}', 'Taxes@show');
    Route::post('/', 'Taxes@store');
    Route::put('{id}', 'Taxes@update');
    Route::delete('{id}', 'Taxes@destroy');
});

/* Items */
Route::prefix('items')->group(function(){
    Route::get('/', 'Items@index');
    Route::get('{id}', 'Items@show');
    Route::post('/', 'Items@store');
    Route::put('{id}', 'Items@update');
    Route::delete('{id}', 'Items@destroy');
});

/* Purchases */
Route::prefix('purchases')->group(function(){
    Route::get('/', 'Purchases\Bills@index');
    Route::get('{id}', 'Purchases\Bills@show');
    Route::post('/', 'Purchases\Bills@store');
    Route::put('{id}', 'Purchases\Bills@update');
    Route::delete('{id}', 'Purchases\Bills@destroy');
});

/* Sales */
Route::prefix('sales')->group(function(){
    Route::get('/', 'Sales\Invoices@index');
    Route::get('{id}', 'Sales\Invoices@show');
    Route::post('/', 'Sales\Invoices@store');
    Route::put('{id}', 'Sales\Invoices@update');
    Route::delete('{id}', 'Sales\Invoices@destroy');
});


/* Transactions */
Route::prefix('transactions')->group(function(){
    Route::get('/', 'Transactions@index');
    Route::get('/revenues', 'Transactions@revenues');
    Route::get('/payments', 'Transactions@payments');
    Route::get('{id}', 'Transactions@show');
    Route::post('/', 'Transactions@store');
    Route::put('{id}', 'Transactions@update');
    Route::delete('{id}', 'Transactions@destroy');
});

/* Transfers */
Route::prefix('transfers')->group(function(){
    Route::get('/', 'Transfers@index');
    Route::get('{id}', 'Transfers@show');
    Route::post('/', 'Transfers@store');
    Route::put('{id}', 'Transfers@update');
    Route::delete('{id}', 'Transfers@destroy');
});

/* Dashboard */

Route::prefix('statistics')->group(function(){
    Route::get('/', "Statistics@getLifetime");
    Route::get('/timeline/{timeline}', "Statistics@timeline");
    Route::get('/timeline/{year}/{month}', "Statistics@monthlyTimeline");
    Route::get('/accounts', "Statistics@accounts");
    Route::get('/transactions', "Statistics@transactions");
});

Route::prefix('reports')->group(function(){
    Route::get('/income-expense/{year}', "Reports@profitLoss");
    Route::get('/ledger/{id}', "Reports@ledger");
});

/*Stock Count*/
Route::prefix('stock')->group(function(){
    Route::get('/{id}', 'Stocks@show');
});