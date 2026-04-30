<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Mail\TestMailMailable;
use App\Services\SettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function __construct(
        private SettingService $settingService,
    ) {}

    public function index(): View
    {
        abort_unless(auth()->user()?->can('settings.view'), 403);

        return view('admin.settings.index');
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('app_logo')) {
            $data['app_logo'] = $request->file('app_logo')->store('settings', 'public');
        }

        foreach ([
            'allow_registration',
            'allow_multiple_hackathons',
            'enable_google_oauth',
            'enable_submissions',
            'enable_judging',
            'enable_leaderboard',
        ] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $this->settingService->setMany($data);

        return back()->with('success', 'Settings saved');
    }

    public function clearCache(): RedirectResponse
    {
        abort_unless(auth()->user()?->can('cache.clear'), 403);

        Cache::flush();
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return back()->with('success', 'Cache cleared successfully');
    }

    public function sendTestEmail(): RedirectResponse
    {
        abort_unless(auth()->user()?->can('settings.update'), 403);

        try {
            $supportEmail = $this->settingService->get('support_email');
            if (! $supportEmail) {
                return back()->with('error', 'Support email is not configured.');
            }

            Mail::to($supportEmail)->send(new TestMailMailable());

            return back()->with('success', "Test email sent to {$supportEmail}");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: '.$e->getMessage());
        }
    }
}
