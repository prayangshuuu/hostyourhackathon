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
        $this->app->singleton(\App\Services\SettingService::class);
        $this->app->singleton(\App\Services\HackathonModeService::class);
        $this->app->singleton(\App\Services\AnnouncementService::class);
        $this->app->singleton(\App\Services\BanService::class);
        $this->app->singleton(\App\Services\HackathonService::class);
        $this->app->singleton(\App\Services\HackathonStatusTransitionService::class);
        $this->app->singleton(\App\Services\ScoringService::class);
        $this->app->singleton(\App\Services\SubmissionService::class);
        $this->app->singleton(\App\Services\TeamService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(SettingService $settings): void
    {
        View::composer('*', function ($view) {
            $mode = app(\App\Services\HackathonModeService::class);
            $view->with([
                'isSingleMode' => $mode->isSingleMode(),
                'singleHackathon' => $mode->isSingleMode() ? $mode->getActiveHackathon() : null,
                'appSettings' => app(SettingService::class),
                'unreadCount' => auth()->check() ? auth()->user()->unreadNotifications()->count() : 0,
            ]);
        });

        $this->app->booted(function () use ($settings) {
            Config::set('app.name', $settings->get('app_name', 'HostYourHackathon'));

            Config::set('mail.mailers.smtp.host', $settings->get('smtp_host'));
            Config::set('mail.mailers.smtp.port', $settings->get('smtp_port', 587));
            Config::set('mail.mailers.smtp.username', $settings->get('smtp_username'));
            Config::set('mail.mailers.smtp.password', $settings->get('smtp_password'));
            Config::set('mail.mailers.smtp.encryption', $settings->get('smtp_encryption', 'tls'));
            Config::set('mail.from.name', $settings->get('mail_from_name'));
            Config::set('mail.from.address', $settings->get('mail_from_address'));
            Config::set('mail.default', 'smtp');
        });
    }
}
