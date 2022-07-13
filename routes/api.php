<?php

use App\Http\Controllers\Api\AccountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * These endpoints require user authentication to continue
 */
Route::middleware('auth:api')->group(function () {

    // Return info about the authenticated user
    Route::get('account', [AccountController::class, 'show']);
});

// Catch all to return a proper 404 on pages not found / invalid endpoints
Route::any('{any}', [App\Http\Controllers\Controller::class, 'notFound'])->where('any', '.*');
