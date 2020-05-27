<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('dev');
});

Route::namespace('Dev')->prefix('dev')->group(function(){
    Route::view('','login');
    Route::post('login','DocController@loginDev');
    Route::get('doc','DocController@index');
    Route::get('doc_data','DocController@getJsonData');
});