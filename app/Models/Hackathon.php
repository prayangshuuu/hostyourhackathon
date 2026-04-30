<?php

namespace App\Models;

use App\Enums\HackathonStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hackathon extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'title',
        'tagline',
        'description',
        'logo',
        'banner',
        'primary_color',
        'status',
        'allow_solo',
        'min_team_size',
        'max_team_size',
        'registration_opens_at',
        'registration_closes_at',
        'submission_opens_at',
        'submission_closes_at',
        'results_at',
        'leaderboard_public',
        'rules',
        'prizes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => HackathonStatus::class,
            'allow_solo' => 'boolean',
            'leaderboard_public' => 'boolean',
            'min_team_size' => 'integer',
            'max_team_size' => 'integer',
            'registration_opens_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'submission_opens_at' => 'datetime',
            'submission_closes_at' => 'datetime',
            'results_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            HackathonStatus::Published->value,
            HackathonStatus::Ongoing->value,
        ]);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->active();
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function isEndedOrArchived(): bool
    {
        return $this->status === HackathonStatus::Ended || $this->status === HackathonStatus::Archived;
    }

    public function isRegistrationOpen(): bool
    {
        $now = now();

        if ($this->registration_opens_at && $now->lt($this->registration_opens_at)) {
            return false;
        }

        if ($this->registration_closes_at && $now->gt($this->registration_closes_at)) {
            return false;
        }

        return true;
    }

    public function isSubmissionOpen(): bool
    {
        $now = now();

        if ($this->submission_opens_at && $now->lt($this->submission_opens_at)) {
            return false;
        }

        if ($this->submission_closes_at && $now->gt($this->submission_closes_at)) {
            return false;
        }

        return true;
    }

    public function isOwnedByUser(User $user): bool
    {
        if ((int) $this->created_by === (int) $user->id) {
            return true;
        }

        return $this->organizers()->where('users.id', $user->id)->exists();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organizers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hackathon_organizers')
            ->withTimestamps();
    }

    public function segments(): HasMany
    {
        return $this->hasMany(Segment::class)->orderBy('order');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function judges(): HasMany
    {
        return $this->hasMany(Judge::class);
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(ScoringCriterion::class)->orderBy('order');
    }

    /**
     * @deprecated Use criteria()
     */
    public function scoringCriteria(): HasMany
    {
        return $this->criteria();
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class)
            ->orderByRaw("case tier when 'title' then 1 when 'gold' then 2 when 'silver' then 3 when 'bronze' then 4 else 5 end")
            ->orderBy('order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('order');
    }
}
