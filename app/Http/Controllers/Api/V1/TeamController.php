<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\TeamResource;
use App\Models\Hackathon;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TeamController extends Controller
{
    #[OA\Post(
        path: "/api/v1/hackathons/{hackathon}/teams",
        operationId: "createTeam",
        summary: "Create a team for a hackathon",
        security: [["bearerAuth" => []]],
        tags: ["Teams"]
    )]
    #[OA\Parameter(name: "hackathon", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string"),
                new OA\Property(property: "segment_id", type: "integer", nullable: true)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 422, description: "Validation or business logic failed")]
    public function store(Request $request, Hackathon $hackathon, TeamService $teamService): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'segment_id' => ['nullable', 'exists:segments,id'],
        ]);

        try {
            $team = $teamService->createTeam($hackathon, $request->user(), $validated);
            $team->load('members.user');
            return ApiResponse::success(new TeamResource($team), 'Team created successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Get(
        path: "/api/v1/teams/{team}",
        operationId: "getTeam",
        summary: "Get team details",
        security: [["bearerAuth" => []]],
        tags: ["Teams"]
    )]
    #[OA\Parameter(name: "team", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 403, description: "Forbidden")]
    #[OA\Response(response: 404, description: "Not found")]
    public function show(Team $team, Request $request): JsonResponse
    {
        $user = $request->user();
        
        $isMember = $team->members()->where('user_id', $user->id)->exists();
        $isOrganizer = $user->hasRole('organizer') && $team->hackathon->created_by === $user->id;
        $isSuperAdmin = $user->hasRole('super_admin');

        if (!$isMember && !$isOrganizer && !$isSuperAdmin) {
            return ApiResponse::error('Forbidden', [], 403);
        }

        $team->load('members.user', 'segment');
        return ApiResponse::success(new TeamResource($team));
    }

    #[OA\Put(
        path: "/api/v1/teams/{team}",
        operationId: "updateTeam",
        summary: "Update team name (Leader only)",
        security: [["bearerAuth" => []]],
        tags: ["Teams"]
    )]
    #[OA\Parameter(name: "team", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name"],
            properties: [
                new OA\Property(property: "name", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 403, description: "Forbidden")]
    #[OA\Response(response: 422, description: "Validation failed")]
    public function update(Request $request, Team $team, TeamService $teamService): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $teamService->updateName($team, $request->user(), $validated['name']);
            $team->load('members.user');
            return ApiResponse::success(new TeamResource($team), 'Team updated successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Post(
        path: "/api/v1/teams/join/{invite_code}",
        operationId: "joinTeam",
        summary: "Join a team via invite code",
        security: [["bearerAuth" => []]],
        tags: ["Teams"]
    )]
    #[OA\Parameter(name: "invite_code", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 422, description: "Validation or business logic failed")]
    public function join(string $inviteCode, Request $request, TeamService $teamService): JsonResponse
    {
        try {
            $team = Team::where('invite_code', $inviteCode)->firstOrFail();
            $teamService->joinTeam($team, $request->user());
            $team->load('members.user');
            return ApiResponse::success(new TeamResource($team), 'Joined team successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Delete(
        path: "/api/v1/teams/{team}/members/{user}",
        operationId: "removeTeamMember",
        summary: "Remove a team member (Leader only)",
        security: [["bearerAuth" => []]],
        tags: ["Teams"]
    )]
    #[OA\Parameter(name: "team", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "user", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 403, description: "Forbidden")]
    public function removeMember(Team $team, User $user, Request $request, TeamService $teamService): JsonResponse
    {
        try {
            $member = $team->members()->where('user_id', $user->id)->firstOrFail();
            $teamService->removeMember($team, $request->user(), $member);
            return ApiResponse::success(null, 'Member removed successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 403);
        }
    }
}
