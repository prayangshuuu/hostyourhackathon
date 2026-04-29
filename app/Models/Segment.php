<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Segment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hackathon_id',
        'name',
        'description',
    ];

    // ───────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function judges(): HasMany
    {
        return $this->hasMany(Judge::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
