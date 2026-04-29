<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\HackathonResource;
use App\Http\Resources\UserResource;
use App\Models\Hackathon;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class AdminController extends Controller
{
    #[OA\Get(
        path: "/api/v1/admin/users",
        operationId: "adminGetUsers",
        summary: "Get list of users",
        security: [["bearerAuth" => []]],
        tags: ["Admin"]
    )]
    #[OA\Parameter(name: "role", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    public function users(Request $request): JsonResponse
    {
        $query = User::query()->withTrashed();

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate((int) $request->input('per_page', 20));

        return ApiResponse::paginated($users, UserResource::class);
    }

    #[OA\Put(
        path: "/api/v1/admin/users/{user}/role",
        operationId: "adminUpdateUserRole",
        summary: "Update a user's role",
        security: [["bearerAuth" => []]],
        tags: ["Admin"]
    )]
    #[OA\Parameter(name: "user", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["role"],
            properties: [
                new OA\Property(property: "role", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    #[OA\Response(response: 422, description: "Validation failed")]
    public function updateUserRole(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return ApiResponse::error('You cannot change your own role', [], 422);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(array_column(RoleEnum::cases(), 'value'))],
        ]);

        $user->syncRoles([$validated['role']]);

        return ApiResponse::success(new UserResource($user), 'Role updated successfully');
    }

    #[OA\Delete(
        path: "/api/v1/admin/users/{user}",
        operationId: "adminDeleteUser",
        summary: "Soft delete a user",
        security: [["bearerAuth" => []]],
        tags: ["Admin"]
    )]
    #[OA\Parameter(name: "user", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Successful operation")]
    public function deleteUser(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return ApiResponse::error('You cannot delete yourself', [], 422);
        }

        $user->delete();

        return ApiResponse::success(null, 'User deleted successfully');
    }

    #[OA\Get(
        path: "/api/v1/admin/hackathons",
        operationId: "adminGetHackathons",
        summary: "Get all hackathons including drafts and archived",
        security: [["bearerAuth" => []]],
        tags: ["Admin"]
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    public function hackathons(Request $request): JsonResponse
    {
        $hackathons = Hackathon::withTrashed()->latest()->paginate((int) $request->input('per_page', 20));

        return ApiResponse::paginated($hackathons, HackathonResource::class);
    }

    #[OA\Get(
        path: "/api/v1/admin/settings",
        operationId: "adminGetSettings",
        summary: "Get all system settings",
        security: [["bearerAuth" => []]],
        tags: ["Admin"]
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    public function getSettings(SettingService $settings): JsonResponse
    {
        // Don't expose smtp password
        $all = $settings->all();
        unset($all['smtp_password']);
        
        return ApiResponse::success($all);
    }

    #[OA\Post(
        path: "/api/v1/admin/settings",
        operationId: "adminUpdateSettings",
        summary: "Update system settings",
        security: [["bearerAuth" => []]],
        tags: ["Admin"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent()
    )]
    #[OA\Response(response: 200, description: "Successful operation")]
    public function updateSettings(Request $request, SettingService $settings): JsonResponse
    {
        $data = $request->except(['_token', '_method']);
        
        foreach ($data as $key => $value) {
            $settings->set($key, $value);
        }

        return ApiResponse::success(null, 'Settings updated successfully');
    }
}
