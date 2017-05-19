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

Route::get('/', function () {
    return view('welcome');
});



Route::group(['prefix' => 'api/v1'], function () {

    Route::get('{table}/{id?}', 'BaseController@issueGetRequest');

    //File and File Types
    Route::post('types/{id?}', 'FileController@saveFileType');
    Route::post('files/{id?}', 'FileController@saveFile');


});
