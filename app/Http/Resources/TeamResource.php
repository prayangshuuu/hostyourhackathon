<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'invite_code' => $this->invite_code,
            'hackathon_id' => $this->hackathon_id,
            'segment' => new SegmentResource($this->whenLoaded('segment')),
            'member_count' => $this->members_count ?? $this->members()->count(),
            'members' => TeamMemberResource::collection($this->whenLoaded('members')),
            'created_at' => $this->created_at,
        ];
    }
}
