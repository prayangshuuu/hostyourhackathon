<?php

namespace App\Services;

use App\Models\Hackathon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HackathonModeService
{
    public function isSingleMode(): bool
    {
        return !app(SettingService::class)->get('allow_multiple_hackathons', true);
    }

    public function getActiveHackathon(): ?Hackathon
    {
        return Cache::remember('active_hackathon', 120, function () {
            return Hackathon::active()
                ->with(['segments.prizes', 'sponsors', 'faqs'])
                ->first();
        });
    }

    public function getSegments(): Collection
    {
        $hackathon = $this->getActiveHackathon();

        if (! $hackathon) {
            return collect();
        }

        return $hackathon->segments
            ->where('is_active', true)
            ->sortBy('order');
    }

    public function invalidateCache(): void
    {
        Cache::forget('active_hackathon');
    }
}
