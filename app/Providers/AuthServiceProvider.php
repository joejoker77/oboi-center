<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Entities\User\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
    public function boot():void
    {
        $this->registerPolicies();
        $this->registerPermissions();

        //
    }

    private function registerPermissions():void
    {
        Gate::define('admin-panel', function (User $user) {
            return $user->userProfile->isAdmin() || $user->userProfile->isModerator();
        });

        Gate::define('verify-user', function (User $user) {
            return $user->isActive();
        });
    }
}
