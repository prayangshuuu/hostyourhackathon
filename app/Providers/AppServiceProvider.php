<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
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
    public function boot(SettingService $settings): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('unreadCount', auth()->user()?->unreadNotifications()->count() ?? 0);
        });

        View::composer('*', function ($view) {
            $view->with('appSettings', app(SettingService::class));
        });

        Config::set('app.name', $settings->get('app_name', 'HostYourHackathon'));

        Config::set('mail.mailers.smtp.host', $settings->get('smtp_host'));
        Config::set('mail.mailers.smtp.port', $settings->get('smtp_port', 587));
        Config::set('mail.mailers.smtp.username', $settings->get('smtp_username'));
        Config::set('mail.mailers.smtp.password', $settings->get('smtp_password'));
        Config::set('mail.mailers.smtp.encryption', $settings->get('smtp_encryption', 'tls'));
        Config::set('mail.from.name', $settings->get('mail_from_name'));
        Config::set('mail.from.address', $settings->get('mail_from_address'));
        Config::set('mail.default', 'smtp');
    }
}
