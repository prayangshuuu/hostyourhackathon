<?php

namespace App\Services;

use App\Models\Hackathon;
use App\Models\User;
use Illuminate\Support\Str;

class HackathonService
{
    /**
     * Check if the user is allowed to create a new hackathon.
     */
    public function canCreateHackathon(User $user): bool
    {
        // Super admin always allowed
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // If multiple active hackathons are not allowed, check if they already have one.
        $settings = app(\App\Services\SettingService::class);
        $allowMultiple = $settings->get('allow_multiple_hackathons', true);

        if (! $allowMultiple) {
            $hasActive = Hackathon::where('created_by', $user->id)
                ->whereIn('status', ['draft', 'published', 'ongoing'])
                ->exists();

            if ($hasActive) {
                return false;
            }
        }

        return true;
    }

    /**
     * Store a new hackathon.
     */
    public function store(User $user, array $data): Hackathon
    {
        if (! $this->canCreateHackathon($user)) {
            throw new \InvalidArgumentException('You already have an active hackathon. Multiple active hackathons are currently disabled.');
        }

        $data['created_by'] = $user->id;
        $data['slug'] = Str::slug($data['title']) . '-' . strtolower(Str::random(6));
        $data['status'] = 'draft';

        // Default allow_solo to false if not provided
        $data['allow_solo'] = $data['allow_solo'] ?? false;

        return Hackathon::create($data);
    }

    /**
     * Update an existing hackathon.
     */
    public function update(Hackathon $hackathon, array $data): bool
    {
        $data['allow_solo'] = $data['allow_solo'] ?? false;
        
        return $hackathon->update($data);
    }
}
