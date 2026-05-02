<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScoringCriterion extends Model
{
    use HasFactory;

    protected $table = 'scoring_criteria';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hackathon_id',
        'segment_id',
        'name',
        'description',
        'max_score',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'integer',
            'order' => 'integer',
        ];
    }

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'criteria_id');
    }
}
