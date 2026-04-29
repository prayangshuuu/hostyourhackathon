<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'hackathon_id',
        'title',
        'problem_statement',
        'description',
        'tech_stack',
        'demo_url',
        'repo_url',
        'is_draft',
        'submitted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_draft' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    // ───────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    // ───────────────────────────────────────────
    // Helpers
    // ───────────────────────────────────────────

    /**
     * Check if the submission window is currently open.
     */
    public function isWindowOpen(): bool
    {
        $hackathon = $this->hackathon;
        $now = now();

        if ($hackathon->submission_opens_at && $now->lt($hackathon->submission_opens_at)) {
            return false;
        }

        if ($hackathon->submission_closes_at && $now->gt($hackathon->submission_closes_at)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the submission can be edited (draft + window open).
     */
    public function isEditable(): bool
    {
        return $this->is_draft && $this->isWindowOpen();
    }

    /**
     * Check if the submission has been finalized.
     */
    public function isFinal(): bool
    {
        return ! $this->is_draft && $this->submitted_at !== null;
    }
}
