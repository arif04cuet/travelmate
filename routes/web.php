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

Route::get('/test', function () {

    $start_date = '10/12/2018';
    $convertedDate = DateTime::createFromFormat('m/d/Y', $start_date)->format('Y-m-d H:i:s');
    return $convertedDate;
});

Route::get('/login/{social}', 'SocialAuthController@getSocialRedirect')
    ->middleware('guest');

Route::get('/login/{social}/callback', 'SocialAuthController@getSocialCallback')
    ->middleware('guest');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
