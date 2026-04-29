<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'visibility' => $this->visibility,
            'segment_id' => $this->segment_id,
            'published_at' => $this->published_at,
            'scheduled_at' => $this->scheduled_at,
        ];
    }
}
