<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Resource;

class AccountController extends Controller
{
    /**
     * @return \App\Http\Resources\Resource
     */
    public function show()
    {
        return new Resource([
            'email' => auth()->user()->email
        ]);
    }
}