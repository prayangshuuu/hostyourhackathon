<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'rank' => $this->resource['rank'] ?? null,
            'team' => new TeamResource($this->resource['team'] ?? null),
            'submission' => new SubmissionResource($this->resource['submission'] ?? null),
            'total_score' => $this->resource['total_score'] ?? null,
        ];
    }
}
