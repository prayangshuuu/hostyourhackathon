<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\HackathonResource;
use App\Models\Hackathon;
use App\Services\HackathonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OrganizerController extends Controller
{
    #[OA\Post(
        path: "/api/v1/organizer/hackathons",
        operationId: "organizerCreateHackathon",
        summary: "Create a new hackathon",
        security: [["bearerAuth" => []]],
        tags: ["Organizer"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["title", "description"],
            properties: [
                new OA\Property(property: "title", type: "string"),
                new OA\Property(property: "description", type: "string"),
                new OA\Property(property: "allow_solo", type: "boolean")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 422, description: "Validation failed")]
    public function storeHackathon(Request $request, HackathonService $hackathonService): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'allow_solo' => 'boolean',
        ]);

        try {
            $hackathon = $hackathonService->store($request->user(), $validated);
            return ApiResponse::success(new HackathonResource($hackathon), 'Hackathon created successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Put(
        path: "/api/v1/organizer/hackathons/{hackathon}",
        operationId: "organizerUpdateHackathon",
        summary: "Update a hackathon",
        security: [["bearerAuth" => []]],
        tags: ["Organizer"]
    )]
    #[OA\Parameter(name: "hackathon", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "title", type: "string"),
                new OA\Property(property: "description", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 422, description: "Validation failed")]
    public function updateHackathon(Request $request, Hackathon $hackathon, HackathonService $hackathonService): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'allow_solo' => 'boolean',
        ]);

        try {
            $hackathonService->update($hackathon, $validated);
            return ApiResponse::success(new HackathonResource($hackathon), 'Hackathon updated successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    // Additional methods like segments, criteria, judges, and announcements can follow similar patterns...
    // To keep the file concise, we provide the main hackathon endpoints as an example of the Organizer role APIs.
}
