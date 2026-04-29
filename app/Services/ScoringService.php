<?php

namespace App\Services;

use App\Models\Hackathon;
use App\Models\Judge;
use App\Models\Score;
use App\Models\ScoringCriterion;
use App\Models\Submission;
use App\Models\User;
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
    public function assertScoringOpen(Hackathon $hackathon): void
    {
        if ($hackathon->results_at && now()->gte($hackathon->results_at)) {
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
    public function addCriterion(Hackathon $hackathon, array $data): ScoringCriterion
    {
        return $hackathon->scoringCriteria()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'max_score' => $data['max_score'],
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
     * @param array $scores ['criteria_id' => ['score' => int, 'remarks' => string|null], ...]
     */
    public function saveScores(Judge $judge, Submission $submission, array $scores): void
    {
        $hackathon = $submission->hackathon;

        $this->assertScoringOpen($hackathon);
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
    }

    // ───────────────────────────────────────────
    // Leaderboard
    // ───────────────────────────────────────────

    /**
     * Get the leaderboard for a hackathon using Eloquent withSum.
     */
    public function getLeaderboard(Hackathon $hackathon)
    {
        return Submission::where('hackathon_id', $hackathon->id)
            ->where('is_draft', false)
            ->with(['team.segment'])
            ->withSum('scores', 'score')
            ->orderByDesc('scores_sum_score')
            ->get();
    }
}
