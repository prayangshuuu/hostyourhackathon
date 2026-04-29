<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Judge extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hackathon_id',
        'user_id',
        'segment_id',
    ];

    // ───────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
