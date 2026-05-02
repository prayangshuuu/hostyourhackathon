<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Segment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hackathon_id',
        'name',
        'description',
        'rules',
        'prizes',
        'rulebook_path',
        'submission_limit',
        'max_teams',
        'registration_opens_at',
        'registration_closes_at',
        'submission_opens_at',
        'submission_closes_at',
        'results_at',
        'is_active',
        'cover_image',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'registration_opens_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'submission_opens_at' => 'datetime',
            'submission_closes_at' => 'datetime',
            'results_at' => 'datetime',
            'is_active' => 'boolean',
            'order' => 'integer',
            'submission_limit' => 'integer',
            'max_teams' => 'integer',
        ];
    }

    // Relationships

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function judges(): HasMany
    {
        return $this->hasMany(Judge::class);
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(ScoringCriterion::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class);
    }

    public function prizeRecords(): HasMany
    {
        return $this->hasMany(SegmentPrize::class)->orderBy('order');
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Helpers

    public function effectiveRegistrationOpensAt(): ?Carbon
    {
        return $this->registration_opens_at ?? $this->hackathon->registration_opens_at;
    }

    public function effectiveRegistrationClosesAt(): ?Carbon
    {
        return $this->registration_closes_at ?? $this->hackathon->registration_closes_at;
    }

    public function effectiveSubmissionOpensAt(): ?Carbon
    {
        return $this->submission_opens_at ?? $this->hackathon->submission_opens_at;
    }

    public function effectiveSubmissionClosesAt(): ?Carbon
    {
        return $this->submission_closes_at ?? $this->hackathon->submission_closes_at;
    }

    public function isRegistrationOpen(): bool
    {
        $open = $this->effectiveRegistrationOpensAt();
        $close = $this->effectiveRegistrationClosesAt();

        if (!$open || !$close) {
            return false;
        }

        return now()->between($open, $close);
    }

    public function isSubmissionOpen(): bool
    {
        $open = $this->effectiveSubmissionOpensAt();
        $close = $this->effectiveSubmissionClosesAt();

        if (!$open || !$close) {
            return false;
        }

        return now()->between($open, $close);
    }

    public function isFull(): bool
    {
        if (!$this->max_teams) {
            return false;
        }

        return $this->teams()->count() >= $this->max_teams;
    }

    public function teamCount(): int
    {
        return $this->teams()->count();
    }

    public function submissionCount(): int
    {
        return $this->submissions()->whereNotNull('submitted_at')->count();
    }
}
