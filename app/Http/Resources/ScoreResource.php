<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'criteria_id' => $this->criteria_id,
            'criteria_name' => $this->whenLoaded('scoringCriterion', function () {
                return $this->scoringCriterion->name;
            }),
            'max_score' => $this->whenLoaded('scoringCriterion', function () {
                return $this->scoringCriterion->max_score;
            }),
            'score' => $this->score,
            'remarks' => $this->remarks,
        ];
    }
}
