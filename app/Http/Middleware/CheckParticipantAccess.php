<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Models\Hackathon;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckParticipantAccess
{
    /**
     * Route names that stay available when there is no active hackathon system-wide.
     *
     * @var list<string>
     */
    private const ALLOWED_WHEN_NO_ACTIVE = [
        'teams.index',
        'participant.announcements.index',
        'participant.announcements.show',
    ];

    private const NO_ACTIVE_MESSAGE = 'Teams and submissions are unavailable while no hackathons are running. You can still review your dashboard and history.';

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasAnyRole([
            RoleEnum::SuperAdmin->value,
            RoleEnum::Organizer->value,
            RoleEnum::Judge->value,
        ])) {
            return $next($request);
        }

        if (! $user->hasRole(RoleEnum::Participant->value)) {
            return $next($request);
        }

        if (Hackathon::active()->exists()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName && in_array($routeName, self::ALLOWED_WHEN_NO_ACTIVE, true)) {
            return $next($request);
        }

        return $this->denyParticipantAction($request);
    }

    protected function denyParticipantAction(Request $request): Response|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => self::NO_ACTIVE_MESSAGE], 403);
        }

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            abort(403, self::NO_ACTIVE_MESSAGE);
        }

        return redirect()->route('dashboard')->with('info', self::NO_ACTIVE_MESSAGE);
    }
}
