<?php

use App\Http\Controllers\Controller;
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

/*
 * 404 page to be used by front-end through redirection
 */
Route::get('/unauthorized', [Controller::class, 'unauthorized']);

/*
 * 404 page to be used by front-end through redirection
 */
Route::get('/not-found', [Controller::class, 'notFound']);

/*
 * Home is a static page presenting the website (see logic if auth VS not-auth)
 */
Route::get('/', HomeController::class);

/*
 * Catch-all to redirect to the Vue application
 */
// todo : Route::view('{any}', 'application')->where('any', '.*')->middleware('auth');
