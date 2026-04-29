<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFile extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'submission_id',
        'file_path',
        'file_type',
        'original_name',
    ];

    // ───────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
