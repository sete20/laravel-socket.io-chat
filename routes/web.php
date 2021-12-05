<?php

use App\User;
use Illuminate\Support\Facades\Route;
use Predis\Client;
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

Route::get('/test', function () {
    // $redis = new \Predis\Client();
    // return $redis->ping();
    return $user = User::find(1)->with(['receivedMessages', 'messages'])->first();
});
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
route::get('optimize', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
});
Route::get('/home', 'HomeController@index')->name('home');
Route::get('conversation/{user}', 'MessageController@conversation')->name('message.conversation')->middleware('auth');
Route::post('message/create', 'MessageController@store')->name('message.store')->middleware('auth');
