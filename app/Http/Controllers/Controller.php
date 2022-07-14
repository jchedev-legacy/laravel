<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Helper to return the request user
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
     * @param $message
     * @return void
     */
    public function notFound($message = null): void
    {
        abort(404, $message ?: 'Page Not Found');
    }

    /**
     * Helper to return an Unauthorized (403) response
     *
     * @param $message
     * @return void
     */
    public function unauthorized($message = null): void
    {
        abort(403, $message ?: 'Unauthorized');
    }
}
