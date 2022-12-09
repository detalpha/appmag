<?php

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

Route::get('/', 'HomeController@index');

// Route::get('/debug-sentry', function () {
//     throw new Exception('My first Sentry error!');
// });


Route::get('/tester', 'TesterController@index');

Auth::routes();

Route::group(['prefix' => 'master','middleware' => 'auth'], function(){
    
    /** 
     * Gardu Controller 
     **/
    Route::resource('/gardu', 'GarduIndukController');

    /** 
     * Penyulang Controller
     **/
    Route::resource('/penyulang', 'PenyulangController');
});

Route::group(['middleware' => 'auth'], function(){
    
    Route::get('mstgijson', 'GarduIndukController@mstgijson')->name('mstgijson');
    Route::get('penyulangjson', 'PenyulangController@penyulangjson')->name('penyulangjson');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('changePassword','HomeController@showChangePasswordForm')->name('changePassword');
    Route::post('changePassword','HomeController@changePassword');
    
    /** 
     * Cek Arus Controller
     **/
    Route::get('proses/cekgangguan', 'CheckArusController@form_gangguan');
    Route::post('cekgangguan', 'CheckArusController@perhitungan');
});