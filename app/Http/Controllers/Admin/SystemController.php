<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function show(): View
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'support_email' => config('mail.from.address'),
            'allow_registration' => config('app.allow_registration', true),
            'max_upload_size' => config('app.max_upload_size', 10),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
            'support_email' => 'required|email|max:255',
        ]);

        $this->updateEnv([
            'APP_NAME' => '"' . $request->app_name . '"',
            'APP_URL' => $request->app_url,
            'MAIL_FROM_ADDRESS' => '"' . $request->support_email . '"',
        ]);

        return back()->with('success_general', 'General settings updated.');
    }

    public function updateRegistration(Request $request): RedirectResponse
    {
        $this->updateEnv([
            'ALLOW_REGISTRATION' => $request->has('allow_registration') ? 'true' : 'false',
        ]);

        return back()->with('success_registration', 'Registration settings updated.');
    }

    public function updateUploads(Request $request): RedirectResponse
    {
        $request->validate([
            'max_upload_size' => 'required|integer|min:1|max:100',
        ]);

        $this->updateEnv([
            'MAX_UPLOAD_SIZE' => $request->max_upload_size,
        ]);

        return back()->with('success_uploads', 'Upload settings updated.');
    }

    /**
     * Update .env file values.
     */
    protected function updateEnv(array $values): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
