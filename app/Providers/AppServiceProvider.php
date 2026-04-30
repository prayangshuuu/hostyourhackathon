<?php

namespace App\Providers;

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
    public function boot(\App\Services\SettingService $settings): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $view->with('unreadCount', auth()->user()?->unreadNotifications()->count() ?? 0);
        });

        \Illuminate\Support\Facades\View::composer('*', function ($view) use ($settings) {
            $view->with('settings', $settings);
        });

        if ($settings->get('smtp_host')) {
            config([
                'mail.mailers.smtp.host' => $settings->get('smtp_host'),
                'mail.mailers.smtp.port' => $settings->get('smtp_port', 587),
                'mail.mailers.smtp.encryption' => $settings->get('smtp_encryption', 'tls') === 'none' ? null : $settings->get('smtp_encryption', 'tls'),
                'mail.mailers.smtp.username' => $settings->get('smtp_username'),
                'mail.mailers.smtp.password' => $settings->get('smtp_password'),
                'mail.from.address' => $settings->get('mail_from_address', config('mail.from.address')),
                'mail.from.name' => $settings->get('mail_from_name', config('mail.from.name')),
            ]);
        }
    }
}
