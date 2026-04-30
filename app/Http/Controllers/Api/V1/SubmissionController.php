<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\SubmissionFileResource;
use App\Http\Resources\SubmissionResource;
use App\Models\Hackathon;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Services\SettingService;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class SubmissionController extends Controller
{
    #[OA\Post(
        path: "/api/v1/hackathons/{hackathon}/submissions",
        operationId: "createSubmission",
        summary: "Create a submission (Leader only)",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"]
    )]
    #[OA\Parameter(name: "hackathon", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["title", "problem_statement", "description"],
            properties: [
                new OA\Property(property: "title", type: "string"),
                new OA\Property(property: "problem_statement", type: "string"),
                new OA\Property(property: "description", type: "string"),
                new OA\Property(property: "tech_stack", type: "string", nullable: true),
                new OA\Property(property: "demo_url", type: "string", format: "url", nullable: true),
                new OA\Property(property: "repo_url", type: "string", format: "url", nullable: true),
                new OA\Property(property: "is_draft", type: "boolean", default: true)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 403, description: "Forbidden")]
    #[OA\Response(response: 422, description: "Validation or business logic failed")]
    public function store(Request $request, Hackathon $hackathon, SubmissionService $submissionService, SettingService $settings): JsonResponse
    {
        if (!$settings->get('enable_submissions', true)) {
            return ApiResponse::error('Submissions are currently disabled', [], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'problem_statement' => ['required', 'string'],
            'description' => ['required', 'string'],
            'tech_stack' => ['nullable', 'string'],
            'demo_url' => ['nullable', 'url'],
            'repo_url' => ['nullable', 'url'],
            'is_draft' => ['boolean'],
        ]);

        try {
            $submission = $submissionService->store($hackathon, $request->user(), $validated);
            $submission->load('files', 'team');
            return ApiResponse::success(new SubmissionResource($submission), 'Submission created successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Get(
        path: "/api/v1/submissions/{submission}",
        operationId: "getSubmission",
        summary: "Get submission details",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"]
    )]
    #[OA\Parameter(name: "submission", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 403, description: "Forbidden")]
    #[OA\Response(response: 404, description: "Not found")]
    public function show(Submission $submission, Request $request): JsonResponse
    {
        $user = $request->user();
        
        $isMember = current($user->roles->pluck('name')->toArray()) === 'participant' && 
                    $submission->team->members()->where('user_id', $user->id)->exists();
        
        $isOrganizer = current($user->roles->pluck('name')->toArray()) === 'organizer' && 
                       $submission->hackathon->created_by === $user->id;
                       
        $isSuperAdmin = current($user->roles->pluck('name')->toArray()) === 'super_admin';
        $isJudge = current($user->roles->pluck('name')->toArray()) === 'judge';

        if (!$isMember && !$isOrganizer && !$isSuperAdmin && !$isJudge) {
            return ApiResponse::error('Forbidden', [], 403);
        }

        $submission->load('files', 'team');
        return ApiResponse::success(new SubmissionResource($submission));
    }

    #[OA\Put(
        path: "/api/v1/submissions/{submission}",
        operationId: "updateSubmission",
        summary: "Update a draft submission (Leader only)",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"]
    )]
    #[OA\Parameter(name: "submission", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["title", "problem_statement", "description"],
            properties: [
                new OA\Property(property: "title", type: "string"),
                new OA\Property(property: "problem_statement", type: "string"),
                new OA\Property(property: "description", type: "string"),
                new OA\Property(property: "tech_stack", type: "string", nullable: true),
                new OA\Property(property: "demo_url", type: "string", format: "url", nullable: true),
                new OA\Property(property: "repo_url", type: "string", format: "url", nullable: true),
                new OA\Property(property: "is_draft", type: "boolean", default: true)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 403, description: "Forbidden")]
    #[OA\Response(response: 422, description: "Validation failed")]
    public function update(Request $request, Submission $submission, SubmissionService $submissionService): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'problem_statement' => ['required', 'string'],
            'description' => ['required', 'string'],
            'tech_stack' => ['nullable', 'string'],
            'demo_url' => ['nullable', 'url'],
            'repo_url' => ['nullable', 'url'],
            'is_draft' => ['boolean'],
        ]);

        try {
            $submissionService->update($submission, $request->user(), $validated);
            $submission->load('files', 'team');
            return ApiResponse::success(new SubmissionResource($submission), 'Submission updated successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Post(
        path: "/api/v1/submissions/{submission}/finalize",
        operationId: "finalizeSubmission",
        summary: "Finalize a submission (Leader only)",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"]
    )]
    #[OA\Parameter(name: "submission", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 422, description: "Failed to finalize")]
    public function finalize(Submission $submission, Request $request, SubmissionService $submissionService): JsonResponse
    {
        try {
            $submissionService->finalize($submission, $request->user());
            $submission->load('files', 'team');
            return ApiResponse::success(new SubmissionResource($submission), 'Submission finalized successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Post(
        path: "/api/v1/submissions/{submission}/files",
        operationId: "uploadSubmissionFile",
        summary: "Upload a file to a submission",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"]
    )]
    #[OA\Parameter(name: "submission", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "file", type: "string", format: "binary")
                ]
            )
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 422, description: "Validation failed")]
    public function uploadFile(Request $request, Submission $submission, SubmissionService $submissionService, SettingService $settings): JsonResponse
    {
        $maxMb = $settings->get('max_file_upload_mb', 10);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,ppt,pptx', "max:{$maxMb}000"],
        ]);

        try {
            $file = $submissionService->addFile($submission, $request->user(), $request->file('file'));
            return ApiResponse::success(new SubmissionFileResource($file), 'File uploaded successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    #[OA\Delete(
        path: "/api/v1/submissions/{submission}/files/{file}",
        operationId: "deleteSubmissionFile",
        summary: "Delete a submission file",
        security: [["bearerAuth" => []]],
        tags: ["Submissions"]
    )]
    #[OA\Parameter(name: "submission", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "file", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 422, description: "Failed to delete")]
    public function deleteFile(Submission $submission, SubmissionFile $file, Request $request, SubmissionService $submissionService): JsonResponse
    {
        if ($file->submission_id !== $submission->id) {
            return ApiResponse::error('File does not belong to this submission', [], 422);
        }

        try {
            $submissionService->removeFile($submission, $request->user(), $file);
            return ApiResponse::success(null, 'File deleted successfully');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }
}
