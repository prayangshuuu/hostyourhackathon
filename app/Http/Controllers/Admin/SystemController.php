<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->can('settings.view'), 403);

        return view('admin.settings.index');
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('settings.update'), 403);

        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
            'support_email' => 'required|email|max:255',
            'max_upload_size' => 'required|integer|min:1|max:100',
        ]);

        $this->updateEnv([
            'APP_NAME' => '"' . $request->app_name . '"',
            'APP_URL' => $request->app_url,
            'MAIL_FROM_ADDRESS' => '"' . $request->support_email . '"',
            'ALLOW_REGISTRATION' => $request->has('allow_registration') ? 'true' : 'false',
            'MAX_UPLOAD_SIZE' => $request->max_upload_size,
        ]);

        return back()->with('success', 'Settings updated successfully.');
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

    /**
     * Clear application cache.
     */
    public function clearCache(): \Illuminate\Http\JsonResponse
    {
        abort_unless(auth()->user()?->can('cache.clear'), 403);

        \Artisan::call('optimize:clear');
        return response()->json(['message' => 'Cache cleared successfully.']);
    }

    /**
     * Send test email to admin.
     */
    public function testEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        abort_unless($request->user()->can('settings.update'), 403);

        try {
            \Illuminate\Support\Facades\Mail::raw('This is a test email from ' . config('app.name'), function ($message) use ($request) {
                $message->to($request->user()->email)
                    ->subject('Test Email — ' . config('app.name'));
            });
            return response()->json(['message' => 'Test email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send test email: ' . $e->getMessage()], 500);
        }
    }
}
