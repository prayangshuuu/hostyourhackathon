<?php

namespace App\Services;

use App\Models\Hackathon;
use App\Models\Judge;
use App\Models\Score;
use App\Models\ScoringCriterion;
use App\Models\Segment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ScoringService
{
    // ───────────────────────────────────────────
    // Assertions
    // ───────────────────────────────────────────

    /**
     * Ensure the judge is assigned to the submission's segment.
     *
     * @throws InvalidArgumentException
     */
    public function assertJudgeCanScore(Judge $judge, Submission $submission): void
    {
        $team = $submission->team;

        // If judge has a specific segment, the team must be in that segment
        if ($judge->segment_id !== null && $team->segment_id !== $judge->segment_id) {
            throw new InvalidArgumentException('You are not assigned to score this submission\'s segment.');
        }
    }

    /**
     * Ensure results_at hasn't passed (judge can still score).
     *
     * @throws InvalidArgumentException
     */
    public function assertScoringOpen(Hackathon $hackathon, ?Segment $segment = null): void
    {
        $resultsAt = $segment?->results_at ?? $hackathon->results_at;

        if ($resultsAt && now()->gte($resultsAt)) {
            throw new InvalidArgumentException('Scoring is closed — results have been published.');
        }
    }

    /**
     * Ensure the submission is finalized (not a draft).
     *
     * @throws InvalidArgumentException
     */
    public function assertSubmissionFinalized(Submission $submission): void
    {
        if ($submission->is_draft) {
            throw new InvalidArgumentException('Cannot score a draft submission.');
        }
    }

    // ───────────────────────────────────────────
    // Scoring Criteria (Organizer)
    // ───────────────────────────────────────────

    /**
     * Add a scoring criterion to a hackathon.
     */
    public function addCriterion(Hackathon $hackathon, array $data, ?int $segmentId = null): ScoringCriterion
    {
        return ScoringCriterion::create([
            'hackathon_id' => $hackathon->id,
            'segment_id' => $segmentId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'max_score' => $data['max_score'],
            'order' => $data['order'] ?? 0,
        ]);
    }

    /**
     * Update a scoring criterion.
     */
    public function updateCriterion(ScoringCriterion $criterion, array $data): ScoringCriterion
    {
        $criterion->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? $criterion->description,
            'max_score' => $data['max_score'],
            'order' => $data['order'] ?? $criterion->order,
        ]);

        return $criterion;
    }

    /**
     * Delete a scoring criterion.
     */
    public function deleteCriterion(ScoringCriterion $criterion): void
    {
        $criterion->delete();
    }

    // ───────────────────────────────────────────
    // Judge Assignment (Organizer)
    // ───────────────────────────────────────────

    /**
     * Assign a user as a judge for a hackathon (optionally for a segment).
     */
    public function assignJudge(Hackathon $hackathon, User $user, ?int $segmentId = null): Judge
    {
        $exists = Judge::where('hackathon_id', $hackathon->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            throw new InvalidArgumentException('This user is already assigned as a judge for this hackathon.');
        }

        return Judge::create([
            'hackathon_id' => $hackathon->id,
            'user_id' => $user->id,
            'segment_id' => $segmentId,
        ]);
    }

    /**
     * Remove a judge assignment.
     */
    public function removeJudge(Judge $judge): void
    {
        // Also remove their scores
        $judge->scores()->delete();
        $judge->delete();
    }

    // ───────────────────────────────────────────
    // Scoring (Judge)
    // ───────────────────────────────────────────

    /**
     * Save or update scores for a submission from a judge.
     *
     * @param  array  $scores  ['criteria_id' => ['score' => int, 'remarks' => string|null], ...]
     */
    public function saveScores(Judge $judge, Submission $submission, array $scores): void
    {
        DB::transaction(function () use ($judge, $submission, $scores) {
            $hackathon = $submission->hackathon;

            $this->assertScoringOpen($hackathon, $submission->segment);
            $this->assertSubmissionFinalized($submission);
            $this->assertJudgeCanScore($judge, $submission);

            foreach ($scores as $criteriaId => $data) {
                Score::updateOrCreate(
                    [
                        'judge_id' => $judge->id,
                        'submission_id' => $submission->id,
                        'criteria_id' => $criteriaId,
                    ],
                    [
                        'score' => $data['score'],
                        'remarks' => $data['remarks'] ?? null,
                    ],
                );
            }
        });
    }

    // ───────────────────────────────────────────
    // Leaderboard
    // ───────────────────────────────────────────

    /**
     * Get the leaderboard for a specific segment.
     */
    public function getLeaderboard(Hackathon $hackathon, Segment $segment): \Illuminate\Support\Collection
    {
        return Submission::where('hackathon_id', $hackathon->id)
            ->where('segment_id', $segment->id)
            ->where('is_draft', false)
            ->where('disqualified', false)
            ->with(['team.members.user'])
            ->withSum('scores', 'score')
            ->orderByDesc('scores_sum_score')
            ->get()
            ->map(function ($submission, $index) {
                $submission->rank = $index + 1;
                return $submission;
            });
    }

    /**
     * Get the overall leaderboard for all active segments in a hackathon.
     */
    public function getHackathonLeaderboard(Hackathon $hackathon): \Illuminate\Support\Collection
    {
        return Submission::where('hackathon_id', $hackathon->id)
            ->where('is_draft', false)
            ->where('disqualified', false)
            ->with(['team.members.user', 'segment'])
            ->withSum('scores', 'score')
            ->orderByDesc('scores_sum_score')
            ->get();
    }
}
