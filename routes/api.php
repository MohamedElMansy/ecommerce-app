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

Route::post('/register',  'App\Http\Controllers\AuthController@register');
Route::post('login', 'App\Http\Controllers\AuthController@login');

Route::middleware('jwt.verify')->group(function () {
    Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
    // for only Merchant
   Route::middleware('App\Http\Middleware\CheckMerchantMiddleware')->group(function () {
        //stores
        Route::post('/stores', 'App\Http\Controllers\StoreController@create');
        //products
        Route::post('/products', 'App\Http\Controllers\ProductController@store');
    });
});

//Guest Routes
Route::get('/products', 'App\Http\Controllers\ProductController@index');
Route::get('/products/{id}', 'App\Http\Controllers\ProductController@show');