<?php

use Illuminate\Support\Facades\Route;
// use Modules\Pelapor\Http\Controllers\PelaporController;
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

Route::group(['prefix' => 'pelapor'], function() {
    Route::get('/tambah-laporan', 'PelaporController@create');
    Route::post('/store-laporan', 'PelaporController@store')->name('store-laporan');
    Route::get('/pesan-masuk', 'PelaporController@pesanMasuk');
    Route::post('/reply/{id}', 'PelaporController@balas_pelapor')->name('balas_pelapor');
    Route::get('/pesan-keluar', 'PelaporController@pesanKeluar');
});
