<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Entities\Admin;

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

Route::group(['prefix' => 'admin'], function() {
    Route::get('/dashboard', 'AdminController@dashboard');
    Route::get('/pesan', 'AdminController@pesan');
    Route::get('/index-unit', 'AdminController@index_unit');
    Route::post('/tambah-unit', 'AdminController@store_unit')->name('unit_store');
    Route::put('/edit_unit/{id}', 'AdminController@edit_unit')->name('edit_unit');
    Route::delete('/hapus_unit/{id}', 'AdminController@hapus_unit')->name('hapus_unit');
    Route::get('/pesan-masuk-pelapor', 'AdminController@pesan_masuk');
    Route::post('/reply/{id}', 'AdminController@balas')->name('reply');
    Route::get('/pesan-keluar-pelapor', 'AdminController@pesan_keluar');
    Route::post('/{id}/update', 'AdminController@teruskanLaporan')->name('teruskan_laporan');
    Route::get('/pesan-masuk-unit', 'AdminController@pesan_masuk_unit');
    Route::get('/send-whatsapp/{complaint_id}', 'AdminController@sendWhatsApp')->name('send.wa');
    Route::get('/pesan-keluar-unit', 'AdminController@pesan_keluar_unit');
});
