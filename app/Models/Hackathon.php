<?php

namespace App\Models;

use App\Enums\HackathonStatus;
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
     * The attributes that are mass assignable.
     *
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
        're_open_submission',
        'leaderboard_public',
        'results_at',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => HackathonStatus::class,
            'allow_solo' => 'boolean',
            'min_team_size' => 'integer',
            'max_team_size' => 'integer',
            'registration_opens_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'submission_opens_at' => 'datetime',
            'submission_closes_at' => 'datetime',
            're_open_submission' => 'boolean',
            'leaderboard_public' => 'boolean',
            'results_at' => 'datetime',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ───────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function segments(): HasMany
    {
        return $this->hasMany(Segment::class);
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

    public function scoringCriteria(): HasMany
    {
        return $this->hasMany(ScoringCriterion::class);
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function organizers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hackathon_organizers')
            ->withTimestamps();
    }
}
