<?php

namespace App\Models;

use App\Enums\AnnouncementStatus;
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
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => AnnouncementVisibility::class,
            'status' => AnnouncementStatus::class,
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPublished(): bool
    {
        return $this->status === AnnouncementStatus::Published
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function isScheduled(): bool
    {
        return $this->status === AnnouncementStatus::Scheduled;
    }

    public function isDraft(): bool
    {
        return $this->status === AnnouncementStatus::Draft;
    }
}
