<?php

namespace App\Http\Middleware;

use App\Services\HackathonModeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SingleModeOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!app(HackathonModeService::class)->isSingleMode()) {
            abort(404);
        }

        return $next($request);
    }
}
