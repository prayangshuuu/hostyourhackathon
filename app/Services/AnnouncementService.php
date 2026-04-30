<?php

namespace App\Services;

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementVisibility;
use App\Mail\AnnouncementPublishedMail;
use App\Models\Announcement;
use App\Models\Hackathon;
use App\Models\User;
use App\Notifications\AnnouncementPublished;
use Illuminate\Support\Facades\Mail;

class AnnouncementService
{
    // ───────────────────────────────────────────
    // CRUD
    // ───────────────────────────────────────────

    /**
     * Create a new announcement (draft or published).
     */
    public function create(Hackathon $hackathon, array $data, User $author): Announcement
    {
        $scheduledAt = $data['scheduled_at'] ?? null;
        $status = $scheduledAt
            ? AnnouncementStatus::Scheduled
            : AnnouncementStatus::Draft;

        $announcement = $hackathon->announcements()->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'visibility' => $data['visibility'],
            'segment_id' => ($data['visibility'] === AnnouncementVisibility::Segment->value)
                ? $data['segment_id']
                : null,
            'scheduled_at' => $scheduledAt,
            'published_at' => null,
            'status' => $status,
            'created_by' => $author->id,
        ]);

        return $announcement;
    }

    /**
     * Update an existing announcement.
     */
    public function update(Announcement $announcement, array $data): Announcement
    {
        $scheduledAt = $data['scheduled_at'] ?? null;
        $nextStatus = $announcement->status;
        if ($announcement->status !== AnnouncementStatus::Published) {
            $nextStatus = $scheduledAt ? AnnouncementStatus::Scheduled : AnnouncementStatus::Draft;
        }

        $announcement->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'visibility' => $data['visibility'],
            'segment_id' => ($data['visibility'] === AnnouncementVisibility::Segment->value)
                ? $data['segment_id']
                : null,
            'scheduled_at' => $scheduledAt,
            'status' => $nextStatus,
        ]);

        return $announcement;
    }

    /**
     * Delete an announcement.
     */
    public function delete(Announcement $announcement): void
    {
        $announcement->delete();
    }

    // ───────────────────────────────────────────
    // Publish
    // ───────────────────────────────────────────

    /**
     * Publish an announcement — sets published_at and sends notifications.
     */
    public function publish(Announcement $announcement): Announcement
    {
        $publishAt = $announcement->scheduled_at ?? now();

        $announcement->update([
            'published_at' => $publishAt,
            'status' => AnnouncementStatus::Published,
        ]);

        // Send email and in-app notification to eligible users
        $this->notifyEligibleUsers($announcement);

        return $announcement;
    }

    // ───────────────────────────────────────────
    // Visibility Filtering
    // ───────────────────────────────────────────

    /**
     * Get announcements visible to a participant for a hackathon.
     */
    public function getVisibleForParticipant(Hackathon $hackathon, User $user)
    {
        return Announcement::where('hackathon_id', $hackathon->id)
            ->where('status', AnnouncementStatus::Published)
            ->whereNotNull('published_at')
            ->where(function ($query) {
                $query->where('scheduled_at', '<=', now())
                    ->orWhereNull('scheduled_at');
            })
            ->where(function ($query) use ($hackathon, $user) {
                $query->where('visibility', AnnouncementVisibility::All->value)
                    ->orWhere(function ($q) use ($hackathon, $user) {
                        // Registered: user is a team member in this hackathon
                        $q->where('visibility', AnnouncementVisibility::Registered->value)
                            ->whereExists(function ($sub) use ($hackathon, $user) {
                                $sub->selectRaw('1')
                                    ->from('team_members')
                                    ->join('teams', 'teams.id', '=', 'team_members.team_id')
                                    ->where('teams.hackathon_id', $hackathon->id)
                                    ->where('team_members.user_id', $user->id);
                            });
                    })
                    ->orWhere(function ($q) use ($hackathon, $user) {
                        // Segment: user is in a team assigned to the announcement's segment
                        $q->where('visibility', AnnouncementVisibility::Segment->value)
                            ->whereExists(function ($sub) use ($hackathon, $user) {
                                $sub->selectRaw('1')
                                    ->from('team_members')
                                    ->join('teams', 'teams.id', '=', 'team_members.team_id')
                                    ->where('teams.hackathon_id', $hackathon->id)
                                    ->where('team_members.user_id', $user->id)
                                    ->whereColumn('teams.segment_id', 'announcements.segment_id');
                            });
                    });
            })
            ->latest('published_at')
            ->get();
    }

    // ───────────────────────────────────────────
    // Notifications (private)
    // ───────────────────────────────────────────

    /**
     * Notify all eligible users via email (queued if configured) and in-app.
     */
    protected function notifyEligibleUsers(Announcement $announcement): void
    {
        $hackathon = $announcement->hackathon;
        $users = $this->resolveRecipients($announcement, $hackathon);

        foreach ($users as $user) {
            // In-app notification (database channel)
            $user->notify(new AnnouncementPublished($announcement));

            // Email notification (sync-safe, queue-compatible)
            Mail::to($user)->send(new AnnouncementPublishedMail($announcement));
        }
    }

    /**
     * Resolve which users should receive the announcement.
     */
    protected function resolveRecipients(Announcement $announcement, Hackathon $hackathon)
    {
        $query = User::query();

        switch ($announcement->visibility) {
            case AnnouncementVisibility::All:
                // All participants with teams in this hackathon
                $query->whereHas('teamMemberships', function ($q) use ($hackathon) {
                    $q->whereHas('team', fn ($t) => $t->where('hackathon_id', $hackathon->id));
                });
                break;

            case AnnouncementVisibility::Registered:
                // Same as all for registered users
                $query->whereHas('teamMemberships', function ($q) use ($hackathon) {
                    $q->whereHas('team', fn ($t) => $t->where('hackathon_id', $hackathon->id));
                });
                break;

            case AnnouncementVisibility::Segment:
                // Only users whose team is in the announcement's segment
                $query->whereHas('teamMemberships', function ($q) use ($hackathon, $announcement) {
                    $q->whereHas('team', function ($t) use ($hackathon, $announcement) {
                        $t->where('hackathon_id', $hackathon->id)
                            ->where('segment_id', $announcement->segment_id);
                    });
                });
                break;
        }

        return $query->get();
    }
}
