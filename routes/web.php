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

Route::get('/main/{token?}', [ 'as' => 'main', 'uses' => 'App\Http\Controllers\MainController@main']);
Route::post('/main/{token?}', [ 'as' => 'main', 'uses' => 'App\Http\Controllers\MainController@main']);

Route::post('/elenco}', [ 'as' => 'elenco', 'uses' => 'App\Http\Controllers\MainController@elenco']);