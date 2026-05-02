<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\HackathonStatus;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\HackathonResource;
use App\Http\Resources\LeaderboardEntryResource;
use App\Models\Hackathon;
use App\Services\ScoringService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class HackathonController extends Controller
{
    #[OA\Get(
        path: '/api/v1/hackathons',
        operationId: 'getHackathonsList',
        summary: 'Get list of hackathons',
        tags: ['Hackathons']
    )]
    #[OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Successful operation')]
    public function index(Request $request): JsonResponse
    {
        $query = Hackathon::query();

        // If not an admin, only show published and ongoing hackathons
        if (! $request->user() || ! $request->user()->hasRole('super_admin')) {
            $statuses = ['published', 'ongoing'];
            if ($request->filled('status')) {
                $requestedStatuses = explode(',', $request->status);
                $statuses = array_intersect($statuses, $requestedStatuses);
            }
            $query->whereIn('status', $statuses ?: ['published', 'ongoing']);
        } else {
            if ($request->filled('status')) {
                $query->whereIn('status', explode(',', $request->status));
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%");
            });
        }

        $hackathons = $query->latest()->paginate((int) $request->input('per_page', 15));

        return ApiResponse::paginated($hackathons, HackathonResource::class);
    }

    #[OA\Get(
        path: '/api/v1/hackathons/{slug}',
        operationId: 'getHackathonBySlug',
        summary: 'Get a hackathon by slug',
        tags: ['Hackathons']
    )]
    #[OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Successful operation')]
    #[OA\Response(response: 404, description: 'Not found')]
    public function show($slug): JsonResponse
    {
        $hackathon = Hackathon::with('segments')->where('slug', $slug)->firstOrFail();

        return ApiResponse::success(new HackathonResource($hackathon));
    }

    #[OA\Get(
        path: '/api/v1/hackathons/{hackathon}/leaderboard',
        operationId: 'getHackathonLeaderboard',
        summary: 'Get hackathon leaderboard',
        security: [['bearerAuth' => []]],
        tags: ['Hackathons']
    )]
    #[OA\Parameter(name: 'hackathon', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Successful operation')]
    #[OA\Response(response: 403, description: 'Forbidden')]
    #[OA\Response(response: 404, description: 'Not found')]
    public function leaderboard(Hackathon $hackathon, Request $request, SettingService $settings, ScoringService $scoringService): JsonResponse
    {
        if (! $settings->get('enable_leaderboard', true)) {
            return ApiResponse::error('Leaderboard is currently disabled', [], 403);
        }

        $user = $request->user();

        $isOrganizer = $user->hasRole('organizer') && $hackathon->created_by === $user->id;
        $isSuperAdmin = $user->hasRole('super_admin');

        if (! $isOrganizer && ! $isSuperAdmin && $hackathon->status !== HackathonStatus::Ended->value) {
            return ApiResponse::error('Leaderboard is not available yet', [], 403);
        }

        $leaderboard = $scoringService->getHackathonLeaderboard($hackathon);

        return ApiResponse::success(LeaderboardEntryResource::collection($leaderboard));
    }
}
