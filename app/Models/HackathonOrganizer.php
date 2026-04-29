<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackathonOrganizer extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'hackathon_id',
        'user_id',
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
}
