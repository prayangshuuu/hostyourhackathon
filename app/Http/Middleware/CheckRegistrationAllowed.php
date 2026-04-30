<?php

namespace App\Http\Middleware;

use App\Models\Hackathon;
use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SettingService::class);

        if (! $settings->get('allow_registration', true)) {
            abort(403, 'Registration is currently disabled.');
        }

        if (! Hackathon::active()->exists()) {
            if ($request->isMethod('GET')) {
                $request->session()->put('url.intended', $request->fullUrl());

                return redirect()->route('login')->with(
                    'info',
                    'Registration is currently closed. No active hackathons are running at this time. If you already have an account, please sign in.'
                );
            }

            abort(403, 'Registration is closed — no active hackathons are running.');
        }

        return $next($request);
    }
}
