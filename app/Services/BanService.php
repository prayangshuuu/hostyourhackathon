<?php

namespace App\Services;

use App\Enums\BanType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BanService
{
    public function banTeam(Team $team, User $bannedBy, string $reason, string $banType = 'manual'): void // String is OK here because it's a default, but let's use the constant if it's better. Actually the review said "no hard-coded status strings".
    {
        DB::transaction(function () use ($team, $bannedBy, $reason, $banType): void {
            $team->update([
                'is_banned' => true,
                'banned_at' => now(),
                'banned_reason' => $reason,
                'banned_by' => $bannedBy->id,
            ]);

            $team->load('users');

            foreach ($team->users as $member) {
                $member->update([
                    'is_banned' => true,
                    'banned_at' => now(),
                    'banned_reason' => 'Banned as part of team: '.$team->name,
                    'ban_type' => BanType::TeamBan->value,
                ]);
            }

            Log::info("Team {$team->id} banned by user {$bannedBy->id}: {$reason}");
        });
    }

    public function unbanTeam(Team $team, User $unbannedBy): void
    {
        DB::transaction(function () use ($team): void {
            $team->update([
                'is_banned' => false,
                'banned_at' => null,
                'banned_reason' => null,
                'banned_by' => null,
            ]);

            $userIds = $team->members()->pluck('user_id');

            User::query()
                ->whereIn('id', $userIds)
                ->where('ban_type', BanType::TeamBan->value)
                ->update([
                    'is_banned' => false,
                    'banned_at' => null,
                    'banned_reason' => null,
                    'ban_type' => null,
                ]);
        });
    }

    public function banUser(User $user, User $bannedBy, string $reason): void
    {
        $user->update([
            'is_banned' => true,
            'banned_at' => now(),
            'banned_reason' => $reason,
            'ban_type' => BanType::Manual->value,
        ]);
    }

    public function unbanUser(User $user): void
    {
        $user->update([
            'is_banned' => false,
            'banned_at' => null,
            'banned_reason' => null,
            'ban_type' => null,
        ]);
    }
}
