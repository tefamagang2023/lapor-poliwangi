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

Route::group(['prefix' => 'UnitPoliwangi'], function() {
    Route::get('/dasboar-unit', 'UnitPoliwangiController@index');
    Route::get('/pesan-masuk-unit', 'UnitPoliwangiController@pesanMasuk')->name('pesanMasuk');
    Route::get('/form/{complaint_id}', 'UnitPoliwangiController@balasPesan')->name('balasPesan');
    Route::post('/response/store', 'UnitPoliwangiController@store')->name('pesan.store');
    Route::get('/pesan-keluar-unit', 'UnitPoliwangiController@pesan_keluar');
});
