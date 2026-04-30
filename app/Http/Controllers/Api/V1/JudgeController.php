<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\ScoreResource;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\ScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class JudgeController extends Controller
{
    #[OA\Get(
        path: '/api/v1/judge/submissions',
        operationId: 'judgeGetSubmissions',
        summary: 'Get assigned submissions for judging',
        security: [['bearerAuth' => []]],
        tags: ['Judge']
    )]
    #[OA\Response(response: 200, description: 'Successful operation')]
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $segmentIds = $user->judgeAssignments()->pluck('segment_id');

        $submissions = Submission::with('team.segment')
            ->whereHas('team', function ($query) use ($segmentIds) {
                $query->whereIn('segment_id', $segmentIds);
            })
            ->where('is_draft', false)
            ->paginate((int) $request->input('per_page', 15));

        return ApiResponse::paginated($submissions, SubmissionResource::class);
    }

    #[OA\Post(
        path: '/api/v1/judge/submissions/{submission}/scores',
        operationId: 'judgeScoreSubmission',
        summary: 'Submit scores for a submission',
        security: [['bearerAuth' => []]],
        tags: ['Judge']
    )]
    #[OA\Parameter(name: 'submission', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'criteria_id', type: 'integer'),
                    new OA\Property(property: 'score', type: 'integer'),
                    new OA\Property(property: 'remarks', type: 'string', nullable: true),
                ]
            )
        )
    )]
    #[OA\Response(response: 200, description: 'Successful operation')]
    #[OA\Response(response: 422, description: 'Validation failed')]
    public function storeScores(Request $request, Submission $submission, ScoringService $scoringService): JsonResponse
    {
        $validated = $request->validate([
            '*.criteria_id' => 'required|exists:scoring_criteria,id',
            '*.score' => 'required|numeric|min:0',
            '*.remarks' => 'nullable|string',
        ]);

        try {
            $scores = $scoringService->scoreSubmission($submission, $request->user(), $validated);

            return ApiResponse::success(ScoreResource::collection($scores), 'Scores saved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Put(
        path: '/api/v1/judge/submissions/{submission}/scores',
        operationId: 'judgeUpdateScores',
        summary: 'Update scores for a submission',
        security: [['bearerAuth' => []]],
        tags: ['Judge']
    )]
    #[OA\Parameter(name: 'submission', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'criteria_id', type: 'integer'),
                    new OA\Property(property: 'score', type: 'integer'),
                    new OA\Property(property: 'remarks', type: 'string', nullable: true),
                ]
            )
        )
    )]
    #[OA\Response(response: 200, description: 'Successful operation')]
    #[OA\Response(response: 422, description: 'Validation failed')]
    public function updateScores(Request $request, Submission $submission, ScoringService $scoringService): JsonResponse
    {
        // Same implementation as store for this particular logic
        return $this->storeScores($request, $submission, $scoringService);
    }
}
