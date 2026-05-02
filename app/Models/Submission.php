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
        'segment_id',
        'title',
        'problem_statement',
        'description',
        'tech_stack',
        'demo_url',
        'repo_url',
        'is_draft',
        'submitted_at',
        're_open_submission',
        'disqualified',
        'disqualified_reason',
        'disqualified_by',
        'disqualified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_draft' => 'boolean',
            're_open_submission' => 'boolean',
            'disqualified' => 'boolean',
            'submitted_at' => 'datetime',
            'disqualified_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function disqualifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disqualified_by');
    }

    public function isEditable(): bool
    {
        $isOpen = $this->segment
            ? $this->segment->isSubmissionOpen()
            : $this->hackathon?->isSubmissionOpen();

        if (!$isOpen) {
            return false;
        }

        return $this->is_draft || $this->re_open_submission;
    }

    public function isFinal(): bool
    {
        return ! $this->is_draft && ! $this->re_open_submission;
    }

    public function totalScore(): int
    {
        return (int) $this->scores()->sum('score');
    }

    /**
     * @deprecated Use segment or hackathon isSubmissionOpen() via isEditable()
     */
    public function isWindowOpen(): bool
    {
        return $this->segment
            ? $this->segment->isSubmissionOpen()
            : (bool) $this->hackathon?->isSubmissionOpen();
    }
}
