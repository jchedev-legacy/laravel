<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->enableAuthenticateAs();
    }

    /**
     * Enable simple authentication on the api with ?authenticate_as=XX
     */
    protected function enableAuthenticateAs()
    {
        if (App::environment() == 'local' && request()->has('authenticate_as') && strstr(request()->path(), 'api/')) {
            $user = User::findOrFail(request()->authenticate_as);

            Auth::guard('api')->setUser($user);
        }
    }
}
