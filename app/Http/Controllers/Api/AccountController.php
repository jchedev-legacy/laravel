<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Resource;

class AccountController extends Controller
{
    public function show()
    {
        $currentUser = $this->user();

        return new Resource([
            'email' => $currentUser->email
        ]);
    }
}
