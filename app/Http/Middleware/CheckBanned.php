<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->is_banned) {
            $reason = $request->user()->banned_reason ?? 'No reason provided.';
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error', 'Your account has been suspended. Reason: '.$reason);
        }

        return $next($request);
    }
}
