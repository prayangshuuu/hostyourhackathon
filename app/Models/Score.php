<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'judge_id',
        'submission_id',
        'criteria_id',
        'score',
        'remarks',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    // ───────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────

    public function judge(): BelongsTo
    {
        return $this->belongsTo(Judge::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(ScoringCriterion::class, 'criteria_id');
    }
}
