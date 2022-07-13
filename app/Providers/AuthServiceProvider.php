<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
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
     * Local helper to easily authenticate as a specific user
     *
     * @return void
     */
    protected function enableAuthenticateAs()
    {
        if (app()->environment('local') && request()->has('authenticate_as')) {
            $user = User::findOrFail(request()->authenticate_as);

            if (str_starts_with(request()->path(), 'api/')) {
                auth('api')->setUser($user);
            } else {
                auth('web')->login($user);
            }
        }
    }
}
