<?php

use Illuminate\Support\Facades\Route;

/*
 * These endpoints require user authentication to continue
 */
Route::middleware('auth:api')->group(function () {

    // Return info about the authenticated user
    Route::get('account', 'AccountController@show');
});

// Catch all to return a proper 404 on pages not found / invalid endpoints
Route::any('{any}', 'Controller@notFound')->where('any', '.*');
