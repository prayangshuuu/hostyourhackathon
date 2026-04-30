<?php

namespace App\Models;

use App\Enums\TeamRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hackathon_id',
        'segment_id',
        'name',
        'invite_code',
        'is_banned',
        'banned_at',
        'banned_reason',
        'banned_by',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
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

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, TeamMember::class, 'team_id', 'id', 'id', 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function submission(): HasOne
    {
        return $this->hasOne(Submission::class);
    }

    public function isFull(): bool
    {
        $max = $this->hackathon?->max_team_size ?? 5;

        return $this->members()->count() >= $max;
    }

    public function leader(): ?User
    {
        $member = $this->members()->where('role', TeamRole::Leader)->first();

        return $member?->user;
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }
}
