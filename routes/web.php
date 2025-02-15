<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/main/{token?}/{dataass?}', [ 'as' => 'main', 'uses' => 'App\Http\Controllers\MainController@main']);
Route::post('/main/{token?}/{dataass?}', [ 'as' => 'main', 'uses' => 'App\Http\Controllers\MainController@main']);


Route::post('save_nota', 'App\Http\Controllers\AjaxController@save_nota');
Route::post('/elenco}', [ 'as' => 'elenco', 'uses' => 'App\Http\Controllers\MainController@elenco']);

Route::post('register_push', [ 'as' => 'register_push', 'uses' => 'App\Http\Controllers\ApiController@register_push']);
Route::get('register_push', [ 'as' => 'register_push', 'uses' => 'App\Http\Controllers\ApiController@register_push']);

Route::post('lav_from_azienda', [ 'as' => 'lav_from_azienda', 'uses' => 'App\Http\Controllers\MainController@lav_from_azienda']);
