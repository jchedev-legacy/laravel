<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function __invoke()
    {
        // if (auth()->user()) {
        //    return view('application');
        // } else {
        return view('home');
        // }
    }
}
