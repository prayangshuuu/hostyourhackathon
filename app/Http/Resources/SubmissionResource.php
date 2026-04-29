<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'problem_statement' => $this->problem_statement,
            'description' => $this->description,
            'tech_stack' => $this->tech_stack,
            'demo_url' => $this->demo_url,
            'repo_url' => $this->repo_url,
            'is_draft' => (bool) $this->is_draft,
            'submitted_at' => $this->submitted_at,
            'files' => SubmissionFileResource::collection($this->whenLoaded('files')),
            'team' => new TeamResource($this->whenLoaded('team')),
            'hackathon_id' => $this->hackathon_id,
            'created_at' => $this->created_at,
        ];
    }
}
