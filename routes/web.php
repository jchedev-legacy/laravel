<?php

use App\Http\Controllers\HomeController;
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

// Home is a static page presenting the website (see logic if auth VS not-auth)
Route::get('/', HomeController::class);

// Route::view('{any}', 'application')->where('any', '.*')->middleware('auth');
