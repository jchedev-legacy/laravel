<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
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
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->enableAuthenticateAs();
    }

    /**
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
