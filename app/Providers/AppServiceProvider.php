<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Gate;
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
        // Implicitly grant all permissions to the super_admin role
        Gate::before(function ($user, $ability) {
            return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
        });

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $view->with('unreadCount', auth()->user()?->unreadNotifications()->count() ?? 0);
        });
    }
}
