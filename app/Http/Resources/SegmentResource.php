<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SegmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'rulebook_url' => $this->rulebook_path ? Storage::url($this->rulebook_path) : null,
            'cover_image_url' => $this->cover_image ? Storage::url($this->cover_image) : null,
            'order' => $this->order,
        ];
    }
}
