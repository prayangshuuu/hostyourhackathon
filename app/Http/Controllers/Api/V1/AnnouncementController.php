<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Hackathon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AnnouncementController extends Controller
{
    #[OA\Get(
        path: "/api/v1/hackathons/{hackathon}/announcements",
        operationId: "getHackathonAnnouncements",
        summary: "Get hackathon announcements",
        security: [["bearerAuth" => []]],
        tags: ["Announcements"]
    )]
    #[OA\Parameter(name: "hackathon", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    public function index(Hackathon $hackathon, Request $request): JsonResponse
    {
        $user = $request->user();
        
        $isOrganizer = current($user->roles->pluck('name')->toArray()) === 'organizer' && $hackathon->created_by === $user->id;
        $isSuperAdmin = current($user->roles->pluck('name')->toArray()) === 'super_admin';

        $query = $hackathon->announcements()->latest();

        if (!$isOrganizer && !$isSuperAdmin) {
            $query->whereIn('status', ['published']);
            
            // Filter by visibility
            $team = $user->teams()->where('hackathon_id', $hackathon->id)->first();
            
            $query->where(function($q) use ($team) {
                $q->where('visibility', 'all');
                
                if ($team) {
                    $q->orWhere('visibility', 'registered');
                    
                    if ($team->segment_id) {
                        $q->orWhere(function($sq) use ($team) {
                            $sq->where('visibility', 'segment')
                               ->where('segment_id', $team->segment_id);
                        });
                    }
                }
            });
        }

        $announcements = $query->paginate((int) $request->input('per_page', 15));

        return ApiResponse::paginated($announcements, AnnouncementResource::class);
    }

    #[OA\Get(
        path: "/api/v1/hackathons/{hackathon}/announcements/{announcement}",
        operationId: "getHackathonAnnouncement",
        summary: "Get a specific announcement",
        security: [["bearerAuth" => []]],
        tags: ["Announcements"]
    )]
    #[OA\Parameter(name: "hackathon", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "announcement", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 404, description: "Not found")]
    public function show(Hackathon $hackathon, Announcement $announcement, Request $request): JsonResponse
    {
        if ($announcement->hackathon_id !== $hackathon->id) {
            return ApiResponse::error('Announcement not found in this hackathon', [], 404);
        }

        return ApiResponse::success(new AnnouncementResource($announcement));
    }
}
