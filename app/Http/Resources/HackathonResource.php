<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class HackathonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'logo_url' => $this->logo ? Storage::url($this->logo) : null,
            'banner_url' => $this->banner ? Storage::url($this->banner) : null,
            'status' => $this->status,
            'allow_solo' => $this->allow_solo,
            'min_team_size' => $this->min_team_size,
            'max_team_size' => $this->max_team_size,
            'segments' => SegmentResource::collection($this->whenLoaded('segments')),
            'registration_opens_at' => $this->registration_opens_at,
            'registration_closes_at' => $this->registration_closes_at,
            'submission_opens_at' => $this->submission_opens_at,
            'submission_closes_at' => $this->submission_closes_at,
            'results_at' => $this->results_at,
            'created_at' => $this->created_at,
        ];
    }
}
