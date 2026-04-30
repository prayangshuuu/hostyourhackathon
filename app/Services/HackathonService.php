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

        $settings = app(SettingService::class);
        if (! $settings->get('allow_multiple_hackathons', true)) {
            $activeCount = Hackathon::active()
                ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->orWhereHas('organizers', fn ($q) => $q->where('user_id', $user->id));
                })
                ->count();

            if ($activeCount >= 1) {
                throw new \InvalidArgumentException('Multiple active hackathons are disabled by the administrator.');
            }
        }

        return true;
    }

    /**
     * Store a new hackathon.
     */
    public function store(User $user, array $data): Hackathon
    {
        $this->canCreateHackathon($user);

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
