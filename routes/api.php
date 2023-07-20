<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Catch all to return a proper 404 on pages not found / invalid endpoints
use Illuminate\Support\Facades\Route;

Route::any('{any}', 'Controller@notFound')->where('any', '.*');
