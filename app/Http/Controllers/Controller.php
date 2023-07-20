<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Shortcut to return the user
     *
     * @return \App\Models\User|null
     */
    public function user(): ?User
    {
        return request()->user();
    }

    /**
     * Helper to return a Not Found (404) response
     *
     * @return void
     */
    public function notFound(): void
    {
        abort(404, 'Page Not Found');
    }

    /**
     * Helper to return an Unauthorized (403) response
     *
     * @return void
     */
    public function unauthorized(): void
    {
        abort(403, 'Unauthorized');
    }
}
