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
    return redirect('/login');
});

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('/login/{social}', 'SocialAuthController@getSocialRedirect')
    ->middleware('guest')->name('social.login');

Route::get('/login/{social}/callback', 'SocialAuthController@getSocialCallback')
    ->middleware('guest');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
