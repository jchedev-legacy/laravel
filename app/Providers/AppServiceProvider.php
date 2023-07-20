<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent Lazy Loading option
        Model::preventLazyLoading(env('PREVENT_LAZY_LOADING'));

        // Useful shortcut to force an HTTP method through the browser
        if (in_array(App::environment(), ['local', 'demo']) && request()->force_method) {
            request()->setMethod(request()->force_method);
        }
    }
}
