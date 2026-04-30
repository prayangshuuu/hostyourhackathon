<?php

namespace App\Models;

use App\Enums\BanType;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
        'is_banned',
        'banned_at',
        'banned_reason',
        'ban_type',
        'email_verified_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
            'ban_type' => BanType::class,
        ];
    }

    public function scopeBanned(Builder $query): Builder
    {
        return $query->where('is_banned', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_banned', false);
    }

    public function isBanned(): bool
    {
        return (bool) $this->is_banned;
    }

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function teams(): HasManyThrough
    {
        return $this->hasManyThrough(
            Team::class,
            TeamMember::class,
            'user_id',
            'id',
            'id',
            'team_id',
        );
    }

    public function createdHackathons(): HasMany
    {
        return $this->hasMany(Hackathon::class, 'created_by');
    }

    public function organizedHackathons(): BelongsToMany
    {
        return $this->belongsToMany(Hackathon::class, 'hackathon_organizers')
            ->withTimestamps();
    }

    public function submissions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Submission::class,
            TeamMember::class,
            'user_id',
            'team_id',
            'id',
            'team_id',
        );
    }

    public function judgeAssignments(): HasMany
    {
        return $this->hasMany(Judge::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function hasTeamInHackathon(Hackathon $hackathon): bool
    {
        return $this->teamMemberships()
            ->whereHas('team', fn (Builder $q) => $q->where('hackathon_id', $hackathon->id))
            ->exists();
    }

    public function teamInHackathon(Hackathon $hackathon): ?Team
    {
        $membership = $this->teamMemberships()
            ->whereHas('team', fn (Builder $q) => $q->where('hackathon_id', $hackathon->id))
            ->with('team')
            ->first();

        return $membership?->team;
    }
}
