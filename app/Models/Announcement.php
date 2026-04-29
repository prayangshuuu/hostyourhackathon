<?php

namespace App\Models;

use App\Enums\AnnouncementVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hackathon_id',
        'title',
        'body',
        'visibility',
        'segment_id',
        'scheduled_at',
        'published_at',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visibility' => AnnouncementVisibility::class,
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    // ───────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ───────────────────────────────────────────
    // Helpers
    // ───────────────────────────────────────────

    /**
     * Check if the announcement has been published.
     */
    public function isPublished(): bool
    {
        return $this->published_at !== null
            && $this->published_at->lte(now());
    }

    /**
     * Check if the announcement is scheduled for the future.
     */
    public function isScheduled(): bool
    {
        return $this->published_at !== null
            && $this->published_at->gt(now());
    }

    /**
     * Check if the announcement is still a draft.
     */
    public function isDraft(): bool
    {
        return $this->published_at === null;
    }

    /**
     * Get the status label.
     */
    public function getStatusAttribute(): string
    {
        if ($this->isDraft()) return 'draft';
        if ($this->isScheduled()) return 'scheduled';
        return 'published';
    }
}
