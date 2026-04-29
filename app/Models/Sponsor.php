<?php

namespace App\Models;

use App\Enums\SponsorTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sponsor extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hackathon_id',
        'name',
        'logo',
        'url',
        'tier',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tier' => SponsorTier::class,
        ];
    }

    // ───────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────

    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }
}
