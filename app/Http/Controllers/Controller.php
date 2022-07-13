<?php

namespace App\Http\Controllers;

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
     * @return mixed
     */
    public function user()
    {
        return request()->user();
    }

    /**
     * Helper to return a Not Found response
     *
     * @return void
     */
    public function notFound()
    {
        abort(404, 'Page Not Found');
    }
}
