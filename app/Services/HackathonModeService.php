<?php

namespace App\Services;

use App\Models\Hackathon;

class HackathonModeService
{
    public function __construct(
        private SettingService $settings,
    ) {}

    public function isSingleMode(): bool
    {
        return ! $this->settings->get('allow_multiple_hackathons', true);
    }

    public function getActiveHackathon(): ?Hackathon
    {
        return Hackathon::active()->first();
    }
}
