<?php

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

/**
 * Authentication routes (register, logic, verification)
 */
Auth::routes();

/**
 * GET route for easy logout
 */
Route::get('/logout', 'Auth\LoginController@logout');

/**
 * Home of the application
 */
Route::get('/', 'HomeController');

// Route::view('{any}', 'application')->where('any', '.*')->middleware('auth');
