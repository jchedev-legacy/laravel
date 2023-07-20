<?php

use Illuminate\Support\Facades\Route;

// Catch all to return a proper 404 on pages not found / invalid endpoints
Route::any('{any}', 'Controller@notFound')->where('any', '.*');
